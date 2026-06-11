<?php
/**
 * ShareViewData.php
 *
 * @copyright  2022 beikeshop.com - All Rights Reserved
 * @link       https://beikeshop.com
 * @author     guangda <service@guangda.work>
 * @created    2022-08-03 15:46:13
 * @modified   2022-08-03 15:46:13
 */

namespace App\Http\Middleware;

use Beike\Repositories\FooterRepo;
use Beike\Repositories\LanguageRepo;
use Beike\Repositories\MenuRepo;
use Beike\Services\DesignService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Beike\Admin\Services\MarketingService;
use Beike\Libraries\ToolCache as Cache;
use Beike\Repositories\SettingRepo;

class ShareViewData
{
    public function handle(Request $request, Closure $next)
    {
        $this->loadShopShareViewData();

        $manager        = app('plugin');
        $enabledPlugins = $manager->getEnabledPlugins();
        $enabledCodes   = $enabledPlugins->pluck('code')->toArray();
        $appDomain      = get_safe_host();

        try {
            $domainObj      = new \Utopia\Domains\Domain($appDomain);
            $registerDomain = $domainObj->getRegisterable();
        } catch (\Exception $e) {
            $registerDomain = '';
        }

        $host = strtolower($request->getHost());
        $serverName = strtolower($request->server('SERVER_NAME'));

        if ($registerDomain && $host === $serverName) {
            $freePluginCodes = config('app.free_plugin_codes') ?? [];
            $enabledCodes    = array_values(array_diff($enabledCodes, $freePluginCodes));
            $this->handleToolSearch($enabledCodes);
        }

        return $next($request);
    }

    /**
     * @throws \Exception
     */
    protected function loadShopShareViewData()
    {
        if (is_admin()) {
            $adminLanguages  = $this->handleAdminLanguages();
            $loggedAdminUser = current_user();
            if ($loggedAdminUser) {
                $currentLanguage = $loggedAdminUser->locale ?: 'en';
                View::share('admin_languages', $adminLanguages);
                View::share('admin_language', collect($adminLanguages)->where('code', $currentLanguage)->first());
            }
        } else {
            View::share('design', request('design') == 1);
            View::share('languages', LanguageRepo::enabled());
            View::share('shop_base_url', shop_route('home.index'));
            View::share('footer_content', hook_filter('footer.content', FooterRepo::handleFooterData()));
            View::share('menu_content', hook_filter('menu.content', MenuRepo::handleMenuData()));
            View::share('drawer_products', $this->loadDrawerProducts());
        }
    }

    /**
     * 从首页第一个 icons 图标模块取卡片数据，供抽屉横滑卡片使用
     * 数据映射：$item['image'] -> 卡片图，$item['text'] -> 卡片名，$item['url'] -> 链接
     */
    private function loadDrawerProducts(): array
    {
        try {
            $modules = system_setting('base.design_setting')['modules'] ?? [];
            foreach ($modules as $module) {
                if (($module['code'] ?? '') === 'icons' && !empty($module['content']['images'])) {
                    $content = DesignService::handleModuleContent('icons', $module['content']);
                    $images  = array_slice($content['images'] ?? [], 0, 4);

                    // 批量取分类图片，避免 N+1
                    $catIds = [];
                    foreach ($images as $img) {
                        if (($img['link']['type'] ?? '') === 'category' && !empty($img['link']['value'])) {
                            $catIds[] = $img['link']['value'];
                        }
                    }
                    $categories = [];
                    if ($catIds) {
                        $categories = \Beike\Models\Category::query()
                            ->whereIn('id', array_unique($catIds))
                            ->pluck('image', 'id')
                            ->toArray();
                    }

                    $items = [];
                    foreach ($images as $img) {
                        $picture = $img['image'] ?? '';
                        $catId   = ($img['link']['type'] ?? '') === 'category' ? ($img['link']['value'] ?? null) : null;
                        if ($catId && !empty($categories[$catId])) {
                            $picture = image_origin($categories[$catId]);
                        }
                        $items[] = [
                            'name'   => $img['text'] ?? '',
                            'url'    => $img['url']  ?? 'javascript:void(0)',
                            'images' => [$picture],
                        ];
                    }
                    return $items;
                }
            }
        } catch (\Throwable $e) {
            // 静默失败，不影响页面渲染
        }
        return [];
    }

    /**
     * 处理后台语言包列表
     *
     * @return array
     */
    private function handleAdminLanguages(): array
    {
        $items     = [];
        $languages = admin_languages();
        foreach ($languages as $language) {
            $path = lang_path("{$language}/admin/base.php");
            if (file_exists($path)) {
                $baseData = require_once $path;
            }
            $name    = $baseData['name'] ?? '';
            $items[] = [
                'code' => $language,
                'name' => $name,
            ];
        }

        return $items;
    }

    private function handleToolSearch($enabledCodes)
    {
        if (!$enabledCodes) {
            return;
        }

        $enabledCodesJson = json_encode($enabledCodes);

        $domain      = get_safe_host();
        $cacheKey    = 'tool_data_' . $domain;
        $cached = Cache::get($cacheKey);

        if (!$cached) {
            if (!$this->limitOnce($domain)) {
                return;
            }

            $result = MarketingService::getInstance()->toolSearch($enabledCodesJson, $domain);
            if (!empty($result['data'])) {
                $cached = $result['data'];
                Cache::put($cacheKey, $cached, now()->addDays(7));
            }
        }

        $this->processToolData($cached, $enabledCodes, $cacheKey);
    }

    private function processToolData($data, $enabledCodes, $cacheKey)
    {
        $data         = $this->formatTool($data);

        if (!$data) {
            return;
        }

        $enabledCodesJson = json_encode($enabledCodes ?? []);
        $pluginsJson      = json_encode(array_keys($data['plugins'] ?? []));
        if ($enabledCodesJson !== $pluginsJson) {
            Cache::forget($cacheKey);
            $this->handleToolSearch($enabledCodes);
            return;
        }

        if ($data['token'] !== system_setting('base.developer_token')) {
            Cache::forget($cacheKey);
            $this->handleToolSearch($enabledCodes);
            return;
        }

        foreach ($data['plugins'] as $code => $isValid) {
            if (!$isValid) {
                SettingRepo::update('plugin', $code, ['status' => false]);
            }
        }
    }

    private function formatTool($data)
    {
        $key = base64_decode(config('beike.website_key'));

        $raw    = base64_decode($data);
        $iv     = substr($raw, 0, 16);
        $cipher = substr($raw, 16);

        if (strlen($iv) !== 16) {
            return null;
        }

        $json = @openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);

        return is_array($data) ? $data : null;
    }

    private function limitOnce($domain)
    {
        $debounceKey = "tool_data_debounce_$domain"; // 防抖
        if (Cache::get($debounceKey)) {
            return false;
        }
        Cache::put($debounceKey, 1, 30);

        $limitKey = "tool_data_limit_$domain";
        $count = Cache::get($limitKey, 0);

        if ($count >= 10) {
            return false;
        }

        Cache::put($limitKey, $count + 1, now()->addMinutes(60));

        return true;
    }
}

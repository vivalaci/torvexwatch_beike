<?php
/**
 * DesignService.php
 *
 * @copyright  2022 beikeshop.com - All Rights Reserved
 * @link       https://beikeshop.com
 * @author     guangda <service@guangda.work>
 * @created    2022-07-14 20:57:37
 * @modified   2022-07-14 20:57:37
 */

namespace Beike\Services;

use Beike\Admin\Repositories\PageRepo;
use Beike\Repositories\BrandRepo;
use Beike\Repositories\ProductRepo;
use Beike\Shop\Http\Resources\BrandDetail;
use Illuminate\Support\Str;

class DesignService
{
    public static function handleRequestModules($modulesData): array
    {
        $modulesData = $modulesData['modules'];
        if (empty($modulesData)) {
            return [];
        }

        foreach ($modulesData as $index => $moduleData) {
            $moduleId = $moduleData['module_id'] ?? '';
            if (empty($moduleId)) {
                $moduleData['module_id'] = Str::random(16);
            }

            $viewPath = $moduleData['view_path'] ?? '';
            if ($viewPath == 'design.') {
                $moduleData['view_path'] = '';
            }

            $modulesData[$index] = $moduleData;
        }

        return ['modules' => $modulesData];
    }

    /**
     * @throws \Exception
     */
    public static function handleModuleContent($moduleCode, $content)
    {
        $productCodes           = ['product', 'category', 'latest'];
        $content['module_code'] = $moduleCode;
        if ($moduleCode == 'slideshow') {
            return self::handleSlideShow($content);
        } elseif (in_array($moduleCode, ['img_text_slideshow', 'img_text_slideshow_2'])) {
            return self::handleImgTextSlideShow($content);
        } elseif ($moduleCode == 'img_text_banner') {
            return self::handleImgTextBanner($content);
        } elseif (in_array($moduleCode, ['image400', 'image401', 'image402', 'image403', 'image100', 'image200', 'image300', 'image301'])) {
            return self::handleBanner($content);
        } elseif ($moduleCode == 'brand') {
            return self::handleBrand($content);
        } elseif ($moduleCode == 'tab_product') {
            return self::handleTabProducts($content);
        } elseif (in_array($moduleCode, $productCodes)) {
            return self::handleProducts($content);
        } elseif (in_array($moduleCode, ['icons', 'img_text_banner_multiple'])) {
            return self::handleIcons($content);
        } elseif ($moduleCode == 'rich_text') {
            return self::handleRichText($content);
        } elseif ($moduleCode == 'page') {
            return self::handlePage($content);
        }

        return hook_filter('service.design.module.content', $content);
    }

    /**
     * 处理 SlideShow 模块
     *
     * @param $content
     * @return array
     * @throws \Exception
     */
    private static function handleSlideShow($content): array
    {
        $images = $content['images'];
        if (empty($images)) {
            return $content;
        }

        $content['images'] = self::handleImages($images);

        return $content;
    }

    /**
     * 处理 ImgTextSlideShow 模块
     *
     * @param $content
     * @return array
     * @throws \Exception
     */
    private static function handleImgTextSlideShow($content): array
    {
        $images = $content['images'];
        $content['scroll_text']['text']  = $content['scroll_text']['text'][locale()] ?? '';

        if (empty($images)) {
            return $content;
        }

        $content['images'] = self::handleImages($images);

        return $content;
    }

    /**
     * 处理 ImgTextBanner 模块
     *
     * @param $content
     * @return array
     * @throws \Exception
     */
    private static function handleImgTextBanner($content): array
    {
        $image = $content['image'];
        if (empty($image)) {
            return $content;
        }

        $content['image']  = is_string($content['image']) ? $content['image'] : $content['image']['src'];
        $content['image_alt']  = $image['alt'][locale()] ?? '';
        $content['title']  = $content['title'][locale()] ?? '';
        $content['image_position']  = $content['image_position'] ?? 'right';
        $content['text_position']  = $content['text_position'] ?? 'left';
        $content['sub_title']  = $content['sub_title'][locale()] ?? '';
        $content['description']  = $content['description'][locale()] ?? '';
        $content['link'] = self::handleLink($content['link']['type'], $content['link']['value']);

        return $content;
    }

    /**
     * 处理 brand 模块
     *
     * @param $content
     * @return array
     * @throws \Exception
     */
    private static function handleBrand($content): array
    {
        $brandIds = $content['brands'] ?? [];
        $brands   = BrandDetail::collection(BrandRepo::getListByIds($brandIds))->jsonSerialize();

        $content['brands'] = $brands;
        $content['title']  = $content['title'][locale()] ?? '';

        return $content;
    }

    /**
     * 处理 SlideShow 模块
     *
     * @param $content
     * @return array
     * @throws \Exception
     */
    private static function handleBanner($content): array
    {
        $images = $content['images'];
        if (empty($images)) {
            return $content;
        }

        $content['images'] = self::handleImages($images);
        $content['full']   = $content['full'] ?? false;
        $content['title']  = $content['title'][locale()] ?? '';
        $content['sub_title']  = $content['sub_title'][locale()] ?? '';

        return $content;
    }

    /**
     * 处理 icons 模块
     *
     * @param $content
     * @return array
     * @throws \Exception
     */
    private static function handleIcons($content): array
    {
        $content['title'] = $content['title'][locale()] ?? '';
        $content['sub_title'] = $content['sub_title'][locale()] ?? '';

        if (empty($content['images'])) {
            $content['lux_icons_carousel'] = self::resolveLuxIconsCarousel($content);

            return $content;
        }

        $images = [];
        foreach ($content['images'] as $image) {
            $images[] = [
                'image'      => image_origin($image['image']['src'] ?? $image['image'] ?? ''),
                'image_alt'  => $image['image']['alt'][locale()] ?? '',
                'text'       => $image['text'][locale()]         ?? '',
                'sub_text'   => $image['sub_text'][locale()]     ?? '',
                'link'       => $image['link'],
                'url'        => self::handleLink($image['link']['type'] ?? '', $image['link']['value'] ?? ''),
            ];
        }

        $content['images'] = $images;
        $content['lux_icons_carousel'] = self::resolveLuxIconsCarousel($content);

        return $content;
    }

    /**
     * 是否使用「Collections 横滑」布局：后台勾选，或标题为 Shop Collections（二开约定，避免仅因 JSON 未写入开关而不生效）
     */
    private static function resolveLuxIconsCarousel(array $content): bool
    {
        if (self::parseLuxIconsCarouselFlag($content['lux_icons_carousel'] ?? false)) {
            return true;
        }

        $title = isset($content['title']) ? trim((string) $content['title']) : '';
        if ($title === '') {
            return false;
        }

        $normalized = mb_strtolower(preg_replace('/\s+/u', ' ', $title));

        return $normalized === 'shop collections';
    }

    /**
     * 图标模块「横滑 Collections」开关（兼容后台 JSON：true/false、1/0、字符串）
     */
    private static function parseLuxIconsCarouselFlag($value): bool
    {
        if ($value === true || $value === 1) {
            return true;
        }
        if ($value === false || $value === 0 || $value === null || $value === '') {
            return false;
        }
        if (is_string($value)) {
            $v = strtolower(trim($value));

            return in_array($v, ['1', 'true', 'yes', 'on'], true);
        }

        return false;
    }

    /**
     * 处理 rich_text 模块
     *
     * @param $content
     * @return array
     * @throws \Exception
     */
    private static function handleRichText($content): array
    {
        $content['data'] = $content['text'][locale()] ?? '';

        return $content;
    }

    /**
     * 处理选项卡商品列表模块
     *
     * @param $content
     * @return array
     */
    private static function handleTabProducts($content): array
    {
        $tabs = $content['tabs'] ?? [];
        if (empty($tabs)) {
            return [];
        }

        foreach ($tabs as $index => $tab) {
            $tabs[$index]['title'] = $tab['title'][locale()] ?? '';
            $productsIds           = $tab['products'];
            if ($productsIds) {
                $tabs[$index]['products'] = ProductRepo::getProductsByIds($productsIds)->jsonSerialize();
            }
        }
        $content['tabs']  = $tabs;
        $content['title'] = $content['title'][locale()] ?? '';

        return $content;
    }

    /**
     * 处理文章模块
     *
     * @param $content
     * @return array
     */
    private static function handlePage($content): array
    {
        $content['title'] = $content['title'][locale()] ?? '';
        $items            = PageRepo::getPagesByIds($content['items'])->jsonSerialize();
        $items            = hook_filter('service.design.module.page.handle', $items);
        $content['items'] = $items;

        return $content;
    }

    /**
     * 处理商品模块
     *
     * @param $content
     * @return array
     * @throws \Exception
     */
    private static function handleProducts($content): array
    {
        $content['products'] = ProductRepo::getProductsByIds($content['products'])->jsonSerialize();
        $content['title']    = $content['title'][locale()] ?? '';

        return $content;
    }

    /**
     * 处理图片以及链接
     * @throws \Exception
     */
    private static function handleImages($images): array
    {
        if (empty($images)) {
            return [];
        }

        foreach ($images as $index => $image) {
            if (is_string($image['image'])) {
                $imagePath = $image['image'];
            } elseif (isset($image['image']['src'])) {
                $imagePath = is_array($image['image']['src']) ? $image['image']['src'][locale()] ?? '' : $image['image']['src'];
            } elseif (is_array($image['image'])) {
                $imagePath = $image['image'][locale()] ?? '';
            }

            $images[$index]['image'] = image_origin($imagePath ?? '');
            $images[$index]['image_path'] = $imagePath ?? '';
            $images[$index]['image_alt'] = $image['image']['alt'][locale()] ?? '';
            $pathForType = strtolower((string) ($imagePath ?? ''));
            $images[$index]['type'] = preg_match('/\.(mp4|m4v|webm|mov|ogv)(\?|#|$)/i', $pathForType) ? 'video' : 'image';
            $images[$index]['sub_title']      = $image['sub_title'][locale()] ?? '';
            $images[$index]['title']          = $image['title'][locale()]     ?? '';
            $images[$index]['description']    = nl2br($image['description'][locale()] ?? '');
            $images[$index]['text_position']  = $image['text_position'] ?? 'start';

            $link = $image['link'] ?? [];
            if (! empty($link['type'] ?? null)) {
                $type                           = $link['type'] ?? '';
                $value                          = $link['type'] == 'custom' || $link['type'] == 'static' ? $link['value'] : ((int) ($link['value'] ?? 0));
                $images[$index]['link']['link'] = self::handleLink($type, $value);
                $images[$index]['link']['text'] = isset($link['text']) && is_array($link['text'])
                    ? ($link['text'][locale()] ?? '')
                    : ($link['text'] ?? '');
                $images[$index]['link']['new_window'] = ! empty($link['new_window']);
            }

            $link2 = $image['link_2'] ?? [];
            if (! empty($link2['type'] ?? null)) {
                $type2                            = $link2['type'] ?? '';
                $value2                           = $link2['type'] == 'custom' || $link2['type'] == 'static' ? ($link2['value'] ?? '') : ((int) ($link2['value'] ?? 0));
                $images[$index]['link_2']['link'] = self::handleLink($type2, $value2);
                $images[$index]['link_2']['text'] = isset($link2['text']) && is_array($link2['text'])
                    ? ($link2['text'][locale()] ?? '')
                    : ($link2['text'] ?? '');
                $images[$index]['link_2']['new_window'] = ! empty($link2['new_window']);
            }
        }

        return $images;
    }

    /**
     * 处理链接
     *
     * @param $type
     * @param $value
     * @return string
     * @throws \Exception
     */
    private static function handleLink($type, $value): string
    {
        return type_route($type, $value);
    }
}

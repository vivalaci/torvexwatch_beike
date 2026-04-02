<?php
/**
 * FooterRepo.php
 *
 * @copyright  2022 beikeshop.com - All Rights Reserved
 * @link       https://beikeshop.com
 * @author     guangda <service@guangda.work>
 * @created    2022-08-11 18:16:06
 * @modified   2022-08-11 18:16:06
 */

namespace Beike\Repositories;

class FooterRepo
{
    /**
     * 保证页尾含 link4（旧数据无 link4 时补齐，避免装修页 Vue 报错）
     *
     * @param  array|null  $footerSetting
     */
    public static function ensureLinkColumns($footerSetting): array
    {
        if (! is_array($footerSetting)) {
            $footerSetting = [];
        }
        if (! isset($footerSetting['content']) || ! is_array($footerSetting['content'])) {
            $footerSetting['content'] = [];
        }
        if (! isset($footerSetting['content']['link4'])) {
            $footerSetting['content']['link4'] = [
                'title' => [],
                'links' => [],
            ];
        }

        return $footerSetting;
    }

    /**
     * 处理页尾编辑器数据
     *
     * @return array|mixed
     */
    public static function handleFooterData($footerSetting = [])
    {
        if (empty($footerSetting)) {
            $footerSetting = system_setting('base.footer_setting');
        }

        $footerSetting = self::ensureLinkColumns($footerSetting ?? []);
        $content         = $footerSetting['content'] ?? [];
        $contentLinkKeys = ['link1', 'link2', 'link3', 'link4'];
        foreach ($contentLinkKeys as $contentLinkKey) {
            $links = $content[$contentLinkKey]['links'] ?? [];
            $links = collect($links)->map(function ($link) {
                return handle_link($link);
            })->toArray();
            $footerSetting['content'][$contentLinkKey]['links'] = $links;
        }

        return $footerSetting;
    }
}

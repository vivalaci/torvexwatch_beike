<?php
/**
 * UploadRequest.php
 *
 * @copyright  2022 beikeshop.com - All Rights Reserved
 * @link       https://beikeshop.com
 * @author     guangda <service@guangda.work>
 * @created    2022-07-22 14:51:27
 * @modified   2022-07-22 14:51:27
 */

namespace Beike\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 不用 Laravel mimes：其依据 guessExtension()，部分 JPG 会被误判为 bin/txt 导致扩展名是 .jpg 仍失败
            'file' => 'required|file',
            'path' => 'nullable|string|max:255',
        ];
    }

    /**
     * 配置验证器实例
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $file = $this->file('file');
            $path = $this->get('path', '');

            if ($file) {
                // 验证文件名安全性
                $originalName = $file->getClientOriginalName();
                if (!$this->isValidFileName($originalName)) {
                    $validator->errors()->add('file', trans('admin/file_manager.invalid_filename'));
                }

                // 验证MIME类型
                if (!$this->isValidMimeType($file)) {
                    $validator->errors()->add('file', trans('admin/file_manager.upload_type_fail'));
                }
            }

            // 验证路径安全性
            if ($path && !$this->isValidPath($path)) {
                $validator->errors()->add('path', trans('admin/file_manager.invalid_path'));
            }
        });
    }

    /**
     * 验证文件名是否安全
     */
    private function isValidFileName(string $fileName): bool
    {
        // 检查文件名长度
        if (strlen($fileName) > 255 || empty(trim($fileName))) {
            return false;
        }

        // 检查危险字符
        if (preg_match('#[<>:"|?*\x00-\x1f]#', $fileName)) {
            return false;
        }

        // 检查路径遍历模式
        if (str_contains($fileName, '..') || str_contains($fileName, '/') || str_contains($fileName, '\\')) {
            return false;
        }

        return true;
    }

    /**
     * 验证类型：扩展名白名单 + MIME（含 Windows 常见 image/pjpeg）+ 与扩展名不符时用文件头魔数兜底
     */
    private function isValidMimeType($file): bool
    {
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'mp4', 'webm'];
        $ext        = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, $allowedExt, true)) {
            return false;
        }

        $allowedMimeTypes = [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/avif',
            'video/mp4',
            'video/quicktime',
            'video/webm',
        ];

        $mime = $file->getMimeType();
        if (in_array($mime, $allowedMimeTypes, true)) {
            return true;
        }

        $guess = $file->guessExtension();
        if ($guess) {
            $g = strtolower($guess);
            if (in_array($g, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'mp4', 'webm'], true)) {
                return true;
            }
        }

        if ($mime === 'application/octet-stream') {
            return $this->fileHeaderMatchesExtension($file, $ext);
        }

        return false;
    }

    /**
     * 用文件头判断是否与声明扩展名一致（处理 finfo 返回 octet-stream 的真实图片/视频）
     */
    private function fileHeaderMatchesExtension($file, string $ext): bool
    {
        $path = $file->getRealPath();
        if (! $path || ! is_readable($path)) {
            return false;
        }

        $head = @file_get_contents($path, false, null, 0, $ext === 'avif' ? 64 : 16);
        if ($head === false || strlen($head) < 3) {
            return false;
        }

        return match ($ext) {
            'jpg', 'jpeg' => strncmp($head, "\xFF\xD8\xFF", 3) === 0,
            'png' => strlen($head) >= 8 && strncmp($head, "\x89PNG\r\n\x1a\n", 8) === 0,
            'gif' => str_starts_with($head, 'GIF87') || str_starts_with($head, 'GIF89'),
            'webp' => strlen($head) >= 12 && substr($head, 0, 4) === 'RIFF' && substr($head, 8, 4) === 'WEBP',
            'avif' => $this->isAvifFileHeader($head),
            'mp4', 'webm' => true,
            default => false,
        };
    }

    /**
     * AVIF：ISO BMFF，ftyp 后 major brand 常为 avif / avis（HEIF 容器）
     */
    private function isAvifFileHeader(string $head): bool
    {
        if (strlen($head) < 12) {
            return false;
        }
        if (substr($head, 4, 4) !== 'ftyp') {
            return false;
        }
        $brand = substr($head, 8, 4);
        if (in_array($brand, ['avif', 'avis'], true)) {
            return true;
        }

        return str_contains(substr($head, 0, 64), 'avif');
    }

    /**
     * 验证路径是否安全
     */
    private function isValidPath(string $path): bool
    {
        // 检查路径遍历
        if (str_contains($path, '..') || str_contains($path, '\\')) {
            return false;
        }

        // 检查危险字符
        if (preg_match('#[<>:"|?*\x00-\x1f]#', $path)) {
            return false;
        }

        return true;
    }
}

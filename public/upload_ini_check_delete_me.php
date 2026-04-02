<?php

/**
 * 浏览器访问本文件可查看「当前处理网站的 PHP」的上传限制与加载的 php.ini。
 * 与命令行执行 php -i 可能不一致 —— 若不一致，请按页面提示修正 PATH 或 Web 服务器使用的 PHP。
 * 核对完毕后请删除本文件，避免对外暴露环境信息。
 */
header('Content-Type: text/plain; charset=utf-8');
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "php_ini_loaded_file: " . (php_ini_loaded_file() ?: '(none — 未加载 php.ini 时上传多为默认 2M)') . "\n";
echo "PHP_BINARY: " . PHP_BINARY . "\n";
echo "PHP_VERSION: " . PHP_VERSION . "\n";

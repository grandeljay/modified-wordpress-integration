<?php

namespace Grandeljay\WordpressIntegration;

if (!\class_exists(__NAMESPACE__ . '\\Constants')) {
    return;
}

if (\rth_is_module_disabled(Constants::MODULE_NAME)) {
    return;
}

if (!isset($PHP_SELF)) {
    return;
}

$current_page  = $PHP_SELF;
$allowed_pages = [
    Constants::BLOG_URL_HOME,
    Constants::BLOG_URL_POSTS,
];

if (!\in_array($current_page, $allowed_pages)) {
    return;
}

$scripts_relative  = 'templates/' . \CURRENT_TEMPLATE . '/javascript';
$scripts_directory = \DIR_FS_CATALOG . $scripts_relative;
$scripts_url       = \DIR_WS_CATALOG . $scripts_relative;
$scripts           = [];

switch ($current_page) {
    case Constants::BLOG_URL_HOME:
        $scripts[] = 'grandeljay_wordpress_integration_blog_home';
        $scripts[] = 'grandeljay_wordpress_integration_blog_listing';

        break;

    case Constants::BLOG_URL_POSTS:
        $scripts[] = 'grandeljay_wordpress_integration_blog_listing';

        break;
}

foreach ($scripts as $filename) {
    $relative = '/' . $filename . '.js';
    $filepath = $scripts_directory . $relative;
    $url      = $scripts_url . $relative;
    $version  = \hash_file('crc32c', $filepath);
    ?>
    <script src="<?= $url ?>?v=<?= $version ?>"></script>
    <?php
}

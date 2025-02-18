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

$stylesheets_relative  = 'templates/' . \CURRENT_TEMPLATE . '/css';
$stylesheets_directory = \DIR_FS_CATALOG . $stylesheets_relative;
$stylesheets_url       = \DIR_WS_CATALOG . $stylesheets_relative;
$stylesheets           = [
    'grandeljay_wordpress_integration_blog_default',
];

switch ($current_page) {
    case Constants::BLOG_URL_HOME:
        $stylesheets[] = 'grandeljay_wordpress_integration_blog_home';
        $stylesheets[] = 'grandeljay_wordpress_integration_blog_listing';

        break;

    case Constants::BLOG_URL_POSTS:
        $stylesheets[] = 'grandeljay_wordpress_integration_blog_listing';

        if (!empty($_GET['post'])) {
            $stylesheets[] = 'grandeljay_wordpress_integration_blog_post';
        }

        break;
}

foreach ($stylesheets as $filename) {
    $relative = '/' . $filename . '.css';
    $filepath = $stylesheets_directory . $relative;
    $url      = $stylesheets_url . $relative;
    $version  = \hash_file('crc32c', $filepath);
    ?>
    <link rel="stylesheet" type="text/css" href="<?= $url ?>?v=<?= $version ?>" />
    <?php
}

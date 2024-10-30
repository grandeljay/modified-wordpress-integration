<?php

namespace Grandeljay\WordpressIntegration;

if (!\class_exists(__NAMESPACE__ . '\\Constants')) {
    return;
}

if (\rth_is_module_disabled(Constants::MODULE_NAME)) {
    return;
}

$stylesheets_relative  = 'templates/' . \CURRENT_TEMPLATE . '/css';
$stylesheets_directory = \DIR_FS_CATALOG . $stylesheets_relative;
$stylesheets_url       = \DIR_WS_CATALOG . $stylesheets_relative;
$stylesheets           = [
    'grandeljay_wordpress_integration_blog_default',
];

switch ($PHP_SELF) {
    case Constants::BLOG_URL_HOME:
        $stylesheets += [
            'grandeljay_wordpress_integration_blog_home',
            'grandeljay_wordpress_integration_blog_listing',
        ];

        break;

    case Constants::BLOG_URL_POSTS:
        $stylesheets[] = 'grandeljay_wordpress_integration_blog_listing';

        if (isset($_GET['post'])) {
            $stylesheets[] = 'grandeljay_wordpress_integration_blog_post';
        }

        if (isset($_GET['search'])) {
            $stylesheets[] = 'grandeljay_wordpress_integration_blog_search_results';
        }

        break;
}

foreach ($stylesheets as $filename) {
    $relative = '/' . $filename . '.css';
    $filepath = $stylesheets_directory . $relative;
    $url      = $stylesheets_url . $relative;
    $version  = \hash_file('crc32c', $filepath);
    ?>
    <link rel="stylesheet" type="text/css" href="<?= $url ?>?v=<?php echo $version ?>" />
    <?php
}

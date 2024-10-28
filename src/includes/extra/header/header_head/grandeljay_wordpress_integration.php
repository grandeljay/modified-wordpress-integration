<?php

namespace Grandeljay\WordpressIntegration;

if (!\class_exists(__NAMESPACE__ . '\\Constants')) {
    return;
}

if (\rth_is_module_disabled(Constants::MODULE_NAME)) {
    return;
}

$allowed_pages = [
    Constants::BLOG_URL_HOME,
    Constants::BLOG_URL_POSTS,
];

if (!\in_array($_SERVER['PHP_SELF'], $allowed_pages, true)) {
    return;
}

$filenames = [
    'templates/' . \CURRENT_TEMPLATE . '/css/grandeljay_wordpress_integration_blog.css',
];

if (isset($_GET['post'])) {
    $filenames = [
        'templates/' . \CURRENT_TEMPLATE . '/css/grandeljay_wordpress_integration_blog_post.css',
    ];
}

if (isset($_GET['search'])) {
    $filenames[] = 'templates/' . \CURRENT_TEMPLATE . '/css/grandeljay_wordpress_integration_blog_search_results.css';
}

foreach ($filenames as $filename) {
    $version = hash_file('crc32c', rtrim(DIR_FS_CATALOG, '/') . '/' . $filename);
    ?>
    <link rel="stylesheet" type="text/css" href="<?= $filename ?>?v=<?php echo $version ?>" />
    <?php
}

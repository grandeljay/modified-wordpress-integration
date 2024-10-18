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

if (!\in_array($PHP_SELF, $allowed_pages, true)) {
    return;
}

$filename = 'templates/' . \CURRENT_TEMPLATE . '/css/blog.css';

if (isset($_GET['post'])) {
    $filename = 'templates/' . \CURRENT_TEMPLATE . '/css/post.css';
}

$version = hash_file('crc32c', rtrim(DIR_FS_CATALOG, '/') . '/' . $filename);
?>
<link rel="stylesheet" type="text/css" href="<?= $filename ?>?v=<?php echo $version ?>" />

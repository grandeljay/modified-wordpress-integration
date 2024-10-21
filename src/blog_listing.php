<?php

/**
 * Blog
 *
 * @author  Jay Trees
 * @link    https://github.com/grandeljay/modified-wordpress-integration
 * @package GrandeljayWordpressIntegration
 */

namespace Grandeljay\WordpressIntegration;

require 'includes/application_top.php';

if (!\class_exists(__NAMESPACE__ . '\\Constants')) {
    return;
}

if (\rth_is_module_disabled(Constants::MODULE_NAME)) {
    return;
}

$smarty = new \Smarty();
$smarty->assign('language', $_SESSION['language']);

$breadcrumb->add('Blog', Constants::BLOG_URL_HOME);
$breadcrumb->add('Beiträge', Constants::BLOG_URL_POSTS);

if (isset($_GET['post'])) {
    $post = Blog::getPost($_GET['post']);

    if (empty($post)) {
        $main_content = $smarty->fetch(CURRENT_TEMPLATE . '/module/blog_post_not_found.html');
    } else {
        $breadcrumb->add($post['title'], $post['link']);

        $smarty->assign('post', $post);

        $main_content = $smarty->fetch(CURRENT_TEMPLATE . '/module/blog_post.html');
    }
} elseif (isset($_GET['categories'])) {
    $options = [
        'categories' => $_GET['categories'],
        'lang'       => $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE,

        'per_page'   => 8,
        'page'       => $_GET['page'] ?? 1,

        'orderby'    => 'date',
        'order'      => 'desc',
    ];

    $main_content = Blog::getPostsHtml($options);
} elseif (isset($_GET['tags'])) {
    $options = [
        'tags'     => $_GET['tags'],
        'lang'     => $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE,

        'per_page' => 8,
        'page'     => $_GET['page'] ?? 1,

        'orderby'  => 'date',
        'order'    => 'desc',
    ];

    $main_content = Blog::getPostsHtml($options);
} else {
    $options = [
        'lang'     => $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE,

        'per_page' => 8,
        'page'     => $_GET['page'] ?? 1,

        'orderby'  => 'date',
        'order'    => 'desc',
    ];

    $main_content = Blog::getPostsHtml($options);
}

require DIR_WS_INCLUDES . 'header.php';
require DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php';

$smarty->assign('main_content', $main_content);
$smarty->display(CURRENT_TEMPLATE . '/index.html');

require 'includes/application_bottom.php';

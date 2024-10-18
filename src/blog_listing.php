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

if (\rth_is_module_disabled(Constants::MODULE_NAME)) {
    return;
}

$smarty = new \Smarty();

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
} else {
    $options = [
        'per_page' => 8,
        'page'     => $_GET['page'] ?? 1,

        'lang'     => $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE,

        'orderby'  => 'date',
        'order'    => 'desc',
    ];

    $posts_with_meta   = Blog::getPosts($options);
    $posts             = $posts_with_meta['posts'];
    $posts_page        = $posts_with_meta['page'];
    $posts_page_links  = [];
    $posts_pages       = $posts_with_meta['total'];
    $posts_pages_total = $posts_with_meta['total_pages'];

    for ($i = 1; $i <= $posts_pages_total; $i++) {
        $url = new Url(Constants::BLOG_URL_POSTS);
        $url->addParameters(
            [
                'language' => $_SESSION['language_code'],
                'page'     => $i,
            ]
        );

        $posts_page_links[$i] = $url->toString();
    }

    if (empty($posts)) {
        \http_response_code(404);

        $breadcrumb->add('404', '#');

        $main_content = $smarty->fetch(CURRENT_TEMPLATE . '/module/blog_posts_not_found.html');
    } else {
        $per_page = \min($options['per_page'], $posts_with_meta['total']);
        $offset   = $per_page * ($options['page'] - 1) + 1;
        $page     = $options['page'];

        $smarty->assign('pagination_offset_start', $offset);
        $smarty->assign('pagination_offset_end', \min($per_page * $page, $posts_with_meta['total']));
        $smarty->assign('pagination_posts_total', $posts_pages);

        $smarty->assign('posts_page', $posts_page);
        $smarty->assign('posts_page_links', $posts_page_links);
        $smarty->assign('posts_pages_total', $posts_pages_total);

        $pagination = $smarty->fetch(CURRENT_TEMPLATE . '/module/blog_post_pagination.html');
        $smarty->assign('pagination', $pagination);

        $smarty->assign('posts', $posts);
        $main_content = $smarty->fetch(CURRENT_TEMPLATE . '/module/blog_post_listing.html');
    }
}

require DIR_WS_INCLUDES . 'header.php';
require DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php';

$smarty->assign('language', $_SESSION['language']);
$smarty->assign('main_content', $main_content);
$smarty->display(CURRENT_TEMPLATE . '/index.html');

require 'includes/application_bottom.php';

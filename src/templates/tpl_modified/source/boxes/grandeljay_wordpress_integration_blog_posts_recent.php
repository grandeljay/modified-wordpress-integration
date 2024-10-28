<?php

/**
 * Blog
 *
 * @author  Jay Trees
 * @link    https://github.com/grandeljay/modified-wordpress-integration
 * @package GrandeljayWordpressIntegration
 */

namespace Grandeljay\WordpressIntegration;

$allowed_pages = [
    Constants::BLOG_URL_HOME,
    Constants::BLOG_URL_POSTS,
];

if (!\in_array($_SERVER['PHP_SELF'], $allowed_pages, true)) {
    return;
}

include \DIR_FS_BOXES_INC . 'smarty_default.php';

/**
 * Get the two most recent blog posts for the current language.
 */
$posts_options   = [
    'per_page' => 2,
    'page'     => 1,

    'lang'     => $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE,

    'orderby'  => 'date',
    'order'    => 'desc',
];
$posts_with_meta = Blog::getPosts($posts_options);
$posts_recent    = \array_map(
    function (Post $post) {
        return $post->toArray();
    },
    $posts_with_meta['posts']
);

$posts_all_link = new Url(Constants::BLOG_URL_POSTS);
$posts_all_link->addDefaultParameters();

require \sprintf(
    '%s/modules/system/%s.php',
    \DIR_WS_LANGUAGES . $_SESSION['language'],
    \grandeljay_wordpress_integration::class
);

$box_smarty->assign('posts_all_link', $posts_all_link->toString());
$box_smarty->assign('posts_all_button', $translations->get('BUTTON_POSTS_VIEW_ALL'));
$box_smarty->assign('posts_recent', $posts_recent);

$box_blog_posts_recent = $box_smarty->fetch(\CURRENT_TEMPLATE . '/boxes/grandeljay_wordpress_integration_blog_posts_recent.html');

$smarty->assign('box_blog_posts_recent', $box_blog_posts_recent);

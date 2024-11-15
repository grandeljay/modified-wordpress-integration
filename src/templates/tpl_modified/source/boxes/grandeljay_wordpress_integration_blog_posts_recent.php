<?php

/**
 * Blog
 *
 * @author  Jay Trees
 * @link    https://github.com/grandeljay/modified-wordpress-integration
 * @package GrandeljayWordpressIntegration
 */

namespace Grandeljay\WordpressIntegration;

if (!isset($PHP_SELF)) {
    return;
}

$allowed_pages = [
    Constants::BLOG_URL_HOME,
    Constants::BLOG_URL_POSTS,
];

if (!\in_array($PHP_SELF, $allowed_pages, true)) {
    return;
}

$unallowed_parameters = [
    'category_id',
    'tag_id',
    'post',
];

foreach ($unallowed_parameters as $parameter) {
    if (isset($_GET[$parameter])) {
        return;
    }
}

include \DIR_FS_BOXES_INC . 'smarty_default.php';

/**
 * Get the two most recent blog posts for the current language.
 */
$posts_options   = [
    'per_page' => 5,
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

$translations = Blog::getModuleTranslations();

$box_smarty->assign('posts_all_link', $posts_all_link->toString());
$box_smarty->assign('posts_all_button', $translations->get('BUTTON_POSTS_VIEW_ALL'));
$box_smarty->assign('posts_recent', $posts_recent);

$box_blog_posts_recent = $box_smarty->fetch(\CURRENT_TEMPLATE . '/boxes/grandeljay_wordpress_integration_blog_posts_recent.html');

$smarty->assign('box_blog_posts_recent', $box_blog_posts_recent);

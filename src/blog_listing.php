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

$translations  = Blog::getModuleTranslations();
$language_code = $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE;

$smarty = new \Smarty();
$smarty->assign('language', $_SESSION['language']);

$breadcrumb->add(
    $translations->get('BLOG'),
    Constants::BLOG_URL_HOME
);

$categories = [];

if (!isset($_GET['post'])) {
    $categories = Blog::getCategories();
}

if (isset($_GET['post'])) {
    $post = Blog::getPost($_GET['post']);

    if (empty($post)) {
        $main_content = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/not_found.html');
    } else {
        $breadcrumb->add($post['title'], $post['link']);

        $smarty->assign('post', $post);

        $main_content = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/template.html');
    }
} elseif (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $category    = $categories[$category_id];

    $breadcrumb->add(
        $category->getName(),
        $category->getLink()
    );

    $posts_options   = [
        'categories' => $category->getIdForLanguage(),
        'per_page'   => 8,
        'page'       => $_GET['page'] ?? 1,
    ];
    $posts_with_meta = Blog::getPosts($posts_options);

    $main_content = Blog::getListingHtml($posts_with_meta, $posts_options);
} elseif (isset($_GET['tag_id'])) {
    $tag_id              = $_GET['tag_id'];
    $tag                 = Blog::getTag($tag_id);
    $tag_translations    = $tag->getTranslations();
    $tag_id_for_language = $tag_translations[$language_code];

    $breadcrumb->add($tag->getName(), $tag->getLink());

    $options = [
        'tags'     => $tag_id_for_language,
        'lang'     => $language_code,

        'per_page' => 8,
        'page'     => $_GET['page'] ?? 1,

        'orderby'  => 'date',
        'order'    => 'desc',
    ];

    $main_content = Blog::getPostsHtml($options);
} elseif (isset($_GET['search'])) {
    $breadcrumb_title = $_GET['search'];
    $breadcrumb_url   = new Url(Constants::BLOG_URL_POSTS);
    $breadcrumb_url->addParameters(['search' => $_GET['search']]);
    $breadcrumb->add($_GET['search'], $breadcrumb_url->toString());

    $options = [
        'search'   => $_GET['search'],
        'type'     => 'post',
        'lang'     => $language_code,

        'per_page' => 8,
        'page'     => $_GET['page'] ?? 1,

        'orderby'  => 'date',
        'order'    => 'desc',
    ];

    $main_content = Blog::getPostsSearchHtml($options);
} else {
    $breadcrumb->add($translations->get('POSTS'), Constants::BLOG_URL_POSTS);

    $options = [
        'lang'     => $language_code,

        'per_page' => 8,
        'page'     => $_GET['page'] ?? 1,

        'orderby'  => 'date',
        'order'    => 'desc',
    ];

    $main_content = Blog::getPostsHtml($options);
}

/**
 * Breadcrumbs must be set before this is called.
 */
require DIR_WS_INCLUDES . 'header.php';

/**
 * Boxes need to be loaded before the
 * `/templates/tpl_modified/module/grandeljay_wordpress_integration/blog/search_result/listing.html`
 * template is fetched, as it is supposed to contain the search box.
 *
 * As a workaround, it is being called explicity in `Blog::getPostsSearchHtml`.
 */
require \DIR_FS_CATALOG . 'templates/' . \CURRENT_TEMPLATE . '/source/boxes.php';

$smarty->assign('main_content', $main_content);
$smarty->display(\CURRENT_TEMPLATE . '/index.html');

require 'includes/application_bottom.php';

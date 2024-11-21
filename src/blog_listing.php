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
    $translations->get('TITLE_BLOG'),
    Constants::BLOG_URL_HOME
);

$categories = [];
$tags       = [];

if (empty($_GET['post'])) {
    $categories = Blog::getCategories();
    $tags       = Blog::getTags();
    $tags_array = \array_map(
        function (Tag $tag) {
            return $tag->toArray();
        },
        $tags
    );

    $smarty->assign('tags', $tags_array);
}

$breadcrumb_title = 'UNKNOWN';
$breadcrumb_link  = \ENABLE_SSL ? \HTTPS_SERVER : \HTTP_SERVER;

if (!empty($_GET['post'])) {
    $post       = Blog::getPost($_GET['post']);
    $post_array = $post->toArray();

    $breadcrumb->add(
        $post->getTitle(),
        $post->getLink()
    );

    if (empty($post)) {
        $main_content = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/not_found.html');
    } else {
        $smarty->assign('post', $post_array);

        $main_content = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/template.html');
    }
} else {
    $breadcrumb_title = $translations->get('TITLE_BLOG_LISTING');
    $breadcrumb_link  = Constants::BLOG_URL_POSTS;
}

$posts_options = [];

if (!empty($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $category    = $categories[$category_id];

    $breadcrumb_title = $category->getName();
    $breadcrumb_link  = $category->getLink();

    $posts_options['categories'] = $category->getIdForLanguage();
}

if (!empty($_GET['tag_id'])) {
    $tag_ids   = $_GET['tag_id'];
    $tag_names = [];

    foreach ($tag_ids as $tag_id) {
        $tag                = $tags[$tag_id];
        $tag_names[$tag_id] = $tag->getName();
    }

    $breadcrumb_url = new Url(Constants::BLOG_URL_POSTS);
    $breadcrumb_url->addDefaultParameters();
    $breadcrumb_url->addParameters($_GET);

    $breadcrumb_title = \implode(', ', $tag_names);
    $breadcrumb_link  = $breadcrumb_url->toString();

    $posts_options['tags'] = \implode(',', $_GET['tag_id']);
}

if (!empty($_GET['search'])) {
    $breadcrumb_url = new Url(Constants::BLOG_URL_POSTS);
    $breadcrumb_url->addParameters(['search' => $_GET['search']]);

    $breadcrumb_title = $_GET['search'];
    $breadcrumb_link  = $breadcrumb_url->toString();

    $posts_options['search']         = $_GET['search'];
    $posts_options['search_columns'] = 'post_title';
}

if (empty($_GET['post'])) {
    $breadcrumb->add($breadcrumb_title, $breadcrumb_link);

    $posts_with_meta = Blog::getPosts($posts_options);

    $main_content = Blog::getListingHtml($posts_with_meta, $posts_options);
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

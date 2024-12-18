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

$translations = Blog::getModuleTranslations();

$smarty = new \Smarty();
$smarty->assign('language', $_SESSION['language']);

$wp_page_front       = Blog::getFrontPage();
$wp_page_front_title = $wp_page_front->getTitle();

$breadcrumb->add(
    $wp_page_front_title,
    Constants::BLOG_URL_HOME
);

$categories = [];
$tags       = [];

if (empty($_GET['post'])) {
    $categories = Blog::getCategories();
    $tags       = Blog::getTags(
        [
            /** WordPress */
            '_fields' => [
                /** WordPress */
                'name',

                /** Polylang */
                'lang',
            ],

            /** Polylang */
            'lang'    => Blog::getLanguageCode(),
        ],
    );
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

$redirect_parameters = [];

if (!empty($_GET['post'])) {
    $post_id = $_GET['post'];
    $post    = Blog::getPost($post_id);

    if (!$post->isInCurrentLanguage() && $post->existsInCurrentLanguage()) {
        $redirect_parameters['language'] = Blog::getLanguageCode();
        $redirect_parameters['post']     = $post->getIdForLanguage();
    }

    $breadcrumb->add(
        $post->getTitle(),
        $post->getLink()
    );

    if (empty($post)) {
        $main_content = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/not_found.html');
    } else {
        $user_is_administrator = '0' === $_SESSION['customers_status']['customers_status_id'];

        if ($user_is_administrator) {
            $post_edit_link = \sprintf(
                'https://blog.hybrid.supply/wp-admin/post.php?post=%d&action=edit',
                $post->getId()
            );

            $smarty->assign('post_edit_link', $post_edit_link);
        }

        $post_array = $post->toArray();

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
    $category    = Blog::getCategory($category_id);

    if (!$category->isInCurrentLanguage() && $category->existsInCurrentLanguage()) {
        $redirect_parameters['language']    = Blog::getLanguageCode();
        $redirect_parameters['category_id'] = $category->getIdForLanguage();
    }

    $breadcrumb_title = $category->getName();
    $breadcrumb_link  = $category->getLink();

    $posts_options['categories'] = $category->getId();
}

if (!empty($_GET['tag_id'])) {
    $tag_ids_request  = $_GET['tag_id'];
    $tag_ids_response = [];
    $tag_names        = [];

    foreach ($tag_ids_request as $tag_id) {
        $tag = Blog::getTag($tag_id);

        if (!$tag->isInCurrentLanguage() && $tag->existsInCurrentLanguage()) {
            $redirect_parameters['language'] = Blog::getLanguageCode();
            $redirect_parameters['tag_id'][] = $tag->getIdForLanguage();
        }

        $tag_ids_response[] = $tag->getId();
        $tag_names[$tag_id] = $tag->getName();
    }

    $breadcrumb_url = new Url(Constants::BLOG_URL_POSTS);
    $breadcrumb_url->addDefaultParameters();
    $breadcrumb_url->addParameters($_GET);

    $breadcrumb_title = \implode(', ', $tag_names);
    $breadcrumb_link  = $breadcrumb_url->toString();

    $posts_options['tags'] = \implode(',', $tag_ids_response);
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

    $smarty->assign('title', $breadcrumb_title);

    $posts_with_meta = Blog::getPosts($posts_options);

    $main_content = Blog::getListingHtml($posts_with_meta, $posts_options);
}

if (!empty($redirect_parameters)) {
    $redirect_url = new Url(Constants::BLOG_URL_POSTS);
    $redirect_url->addParameters($redirect_parameters);

    \header(
        \sprintf(
            'Location: %s',
            $redirect_url->toString()
        )
    );
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

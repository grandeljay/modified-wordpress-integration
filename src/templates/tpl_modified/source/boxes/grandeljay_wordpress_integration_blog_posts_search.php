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

if (Constants::BLOG_URL_HOME !== $PHP_SELF) {
    return;
}

$translations = Blog::getModuleTranslations();

/** Filter */
$tags        = Blog::getTags();
$html_filter = Blog::getFilterHtml($box_smarty, $tags);
$box_smarty->assign('filter', $html_filter);
/** */

$form_action       = Constants::BLOG_URL_POSTS;
$form_search_query = $_GET['search'] ?? '';

$box_smarty->assign('form_action', $form_action);
$box_smarty->assign('form_search_query', $form_search_query);

$box_blog_posts_search = $box_smarty->fetch(\CURRENT_TEMPLATE . '/boxes/grandeljay_wordpress_integration_blog_posts_search.html');

$smarty->assign('box_blog_posts_search', $box_blog_posts_search);

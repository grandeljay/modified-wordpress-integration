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

$translations = Blog::getModuleTranslations();

$form_action            = Constants::BLOG_URL_POSTS;
$form_title             = $translations->get('POSTS_SEARCH_TITLE');
$form_input_placeholder = $translations->get('POSTS_SEARCH');
$form_button_submit     = $translations->get('POSTS_SEARCH_SUBMIT');
$form_search_query      = $_GET['search'] ?? '';

/** Search reset */
$search_reset_parameters = $_GET;

unset($search_reset_parameters['page']);
unset($search_reset_parameters['search']);

$search_reset_server = \ENABLE_SSL ? \HTTPS_SERVER : \HTTP_SERVER;
$search_reset_link   = new Url($search_reset_server . Constants::BLOG_URL_POSTS);
$search_reset_link->addParameters($search_reset_parameters);

$form_link_reset_text = $translations->get('FORM_SEARCH_RESET');
$form_link_reset_url  = $search_reset_link->toString();
/** */

$box_smarty->assign('form_title', $form_title);
$box_smarty->assign('form_action', $form_action);
$box_smarty->assign('form_search_query', $form_search_query);
$box_smarty->assign('form_input_placeholder', $form_input_placeholder);
$box_smarty->assign('form_button_submit', $form_button_submit);
$box_smarty->assign('form_link_reset_url', $form_link_reset_url);
$box_smarty->assign('form_link_reset_text', $form_link_reset_text);

$box_blog_posts_search = $box_smarty->fetch(\CURRENT_TEMPLATE . '/boxes/grandeljay_wordpress_integration_blog_posts_search.html');

$smarty->assign('box_blog_posts_search', $box_blog_posts_search);

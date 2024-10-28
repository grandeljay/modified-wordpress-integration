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

require Blog::getModuleLanguageFilePath();

$form_action            = Constants::BLOG_URL_POSTS;
$form_title             = $translations->get('POSTS_SEARCH_TITLE');
$form_input_placeholder = $translations->get('POSTS_SEARCH');
$form_button_submit     = $translations->get('POSTS_SEARCH_SUBMIT');

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

$form_search_query = $_GET['search'] ?? '';
$form_posts_search = <<<HTML
    <form action="{$form_action}">
        <input type="text" value="{$form_search_query}" name="search" placeholder="{$form_input_placeholder}">

        <input type="submit" value="{$form_button_submit}">
        <a href="{$form_link_reset_url}">{$form_link_reset_text}</a>
    </form>
HTML;

$box_smarty->assign('form_posts_search_title', $form_title);
$box_smarty->assign('form_posts_search', $form_posts_search);

$box_blog_posts_search = $box_smarty->fetch(\CURRENT_TEMPLATE . '/boxes/grandeljay_wordpress_integration_blog_posts_search.html');

$smarty->assign('box_blog_posts_search', $box_blog_posts_search);

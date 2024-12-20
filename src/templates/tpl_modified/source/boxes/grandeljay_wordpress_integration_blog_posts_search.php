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

$html_filter = Blog::getFilterHtml($box_smarty);
$box_smarty->assign('filter', $html_filter);

$box_blog_posts_search = $box_smarty->fetch(\CURRENT_TEMPLATE . '/boxes/grandeljay_wordpress_integration_blog_posts_search.html');

$smarty->assign('box_blog_posts_search', $box_blog_posts_search);

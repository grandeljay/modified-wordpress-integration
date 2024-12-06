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

$smarty = new \Smarty();
$smarty->assign('language', $_SESSION['language']);

$wp_page_front       = Blog::getFrontPage();
$wp_page_front_title = $wp_page_front->getTitle();

$breadcrumb->add($wp_page_front_title, Constants::BLOG_URL_HOME);

$smarty->assign('blog_title', $wp_page_front_title);
$smarty->assign('blog_introduction', Blog::getIntroductionHtml($wp_page_front));
require \DIR_WS_INCLUDES . 'header.php';
require \DIR_FS_CATALOG . 'templates/' . \CURRENT_TEMPLATE . '/source/boxes.php';

$main_content = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/home.html');

$smarty->assign('main_content', $main_content);
$smarty->display(\CURRENT_TEMPLATE . '/index.html');

require 'includes/application_bottom.php';

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

$translations    = Blog::getModuleTranslations();
$text_title_blog = $translations->get('TITLE_BLOG');

$smarty = new \Smarty();
$smarty->assign('language', $_SESSION['language']);
$smarty->assign('introduction', Blog::getIntroductionHtml());

$breadcrumb->add($text_title_blog, Constants::BLOG_URL_HOME);

require \DIR_WS_INCLUDES . 'header.php';
require \DIR_FS_CATALOG . 'templates/' . \CURRENT_TEMPLATE . '/source/boxes.php';

$main_content = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/home.html');

$smarty->assign('main_content', $main_content);
$smarty->display(\CURRENT_TEMPLATE . '/index.html');

require 'includes/application_bottom.php';

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

include \DIR_FS_BOXES_INC . 'smarty_default.php';

$categories_options     = [
    'per_page' => 9,
    'page'     => 1,
    '_embed'   => true,
];
$categories_all         = Blog::getCategories($categories_options);
$categories_categorised = \array_filter(
    $categories_all,
    function (Category $category) {
        return !$category->isUncategorised();
    }
);
$categories_array       = \array_map(
    function (Category $category) {
        return $category->toArray();
    },
    $categories_categorised
);

$box_smarty->assign('categories', $categories_array);

$box_blog_categories = $box_smarty->fetch(\CURRENT_TEMPLATE . '/boxes/grandeljay_wordpress_integration_blog_categories.html');

$smarty->assign('box_blog_categories', $box_blog_categories);

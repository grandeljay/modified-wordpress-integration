<?php

/**
 * Blog
 *
 * @author  Jay Trees
 * @link    https://github.com/grandeljay/modified-wordpress-integration
 * @package GrandeljayWordpressIntegration
 */

namespace Grandeljay\WordpressIntegration;

if (Constants::BLOG_URL_HOME !== $_SERVER['PHP_SELF']) {
    return;
}

include \DIR_FS_BOXES_INC . 'smarty_default.php';

$categories_options = [
    'per_page' => 9,
    'page'     => 1,

    'lang'     => $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE,
    '_embed'   => true,

    'orderby'  => 'count',
    'order'    => 'desc',
];
$categories         = Blog::getCategories($categories_options);
$categories_array   = \array_map(
    function (Category $category) {
        return $category->toArray();
    },
    $categories
);

$box_smarty->assign('categories', $categories_array);

$box_blog_categories = $box_smarty->fetch(\CURRENT_TEMPLATE . '/boxes/grandeljay_wordpress_integration_blog_categories.html');

$smarty->assign('box_blog_categories', $box_blog_categories);

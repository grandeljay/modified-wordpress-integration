<?php

/**
 * Blog
 *
 * @author  Jay Trees
 * @link    https://github.com/grandeljay/modified-wordpress-integration
 * @package GrandeljayWordpressIntegration
 */

namespace Grandeljay\WordpressIntegration;

if (!\class_exists(__NAMESPACE__ . '\\Constants')) {
    return;
}

if (\rth_is_module_disabled(Constants::MODULE_NAME)) {
    return;
}

$includes = [
    'grandeljay_wordpress_integration_blog_posts_recent.php',
    'grandeljay_wordpress_integration_blog_posts_search.php',
];

foreach ($includes as $include) {
    include __DIR__ . '/' . $include;
}

<?php

/**
 * Sets the meta description for blog posts.
 */

namespace Grandeljay\WordpressIntegration;

if (!\class_exists(__NAMESPACE__ . '\\Constants')) {
    return;
}

if (\rth_is_module_disabled(Constants::MODULE_NAME)) {
    return;
}

if (Constants::BLOG_URL_POSTS !== $PHP_SELF) {
    return;
}

if (!isset($post['excerpt'])) {
    return;
}

$metadata_array['description'] = $post['excerpt'];

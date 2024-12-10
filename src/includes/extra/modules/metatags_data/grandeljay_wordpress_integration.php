<?php

/**
 * Performs search engine optimisations.
 *
 * @see /includes/modules/metatags.php
 */

namespace Grandeljay\WordpressIntegration;

if (!\class_exists(__NAMESPACE__ . '\\Constants')) {
    return;
}

if (\rth_is_module_disabled(Constants::MODULE_NAME)) {
    return;
}

if (!isset($PHP_SELF)) {
    return;
}

if (Constants::BLOG_URL_POSTS !== $PHP_SELF) {
    return;
}

if (!isset($post) || !($post instanceof Post)) {
    return;
}

/** Set meta */
$metadata_array['description'] = $post->getExcerpt();

/** Set alternate tag */
$post_translations = $post->getTranslations();

/** To do: add pagination */

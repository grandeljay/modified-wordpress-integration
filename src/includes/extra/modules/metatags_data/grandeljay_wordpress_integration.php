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

$current_page  = $PHP_SELF;
$allowed_pages = [
    Constants::BLOG_URL_HOME,
    Constants::BLOG_URL_POSTS,
];

if (!\in_array($current_page, $allowed_pages)) {
    return;
}

/** Set indexing */
$meta_robots = \META_ROBOTS;

/** Set meta */
$tag_ids = Blog::getTags(
    [
        'lang' => Blog::getLanguageCodeDefault(),
    ]
);

if (isset($post) && $post instanceof Post) {
    $metadata_array['description'] = $post->getExcerpt();
}

/** To do: add pagination */

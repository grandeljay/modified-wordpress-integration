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

$languages = [
    'de',
    'en',
    'es',
    'fr',
    'it',
];

echo '<pre>';

/**
 * Posts
 */
$posts_options   = [
    'lang' => Blog::getLanguageCodeDefault(),
];
$posts_with_meta = Blog::getPosts($posts_options, true);
$posts           = $posts_with_meta['posts'];

$old_id_to_new_id = [
    'blog_cat=2&blog_item=1'  => 1,
    'blog_cat=3&blog_item=2'  => 118,
    'blog_cat=2&blog_item=4'  => 41,
    'blog_cat=2&blog_item=5'  => 49,
    'blog_cat=2&blog_item=6'  => 64,
    'blog_cat=2&blog_item=7'  => 68,
    'blog_cat=2&blog_item=8'  => 86,
    'blog_cat=2&blog_item=9'  => 92,
    'blog_cat=2&blog_item=10' => 95,
    'blog_cat=2&blog_item=12' => 105,
    'blog_cat=2&blog_item=13' => 209,
    'blog_cat=2&blog_item=14' => 234,
    'blog_cat=2&blog_item=15' => 264,
    'blog_cat=2&blog_item=16' => 580,
    'blog_cat=2&blog_item=17' => 588,
    /** No post found for ID 18 */
    'blog_cat=2&blog_item=19' => 652,
    'blog_cat=2&blog_item=20' => 617,
    'blog_cat=2&blog_item=21' => 607,
    'blog_cat=2&blog_item=22' => 596,
    'blog_cat=2&blog_item=23' => 299,
    'blog_cat=2&blog_item=24' => 805,
];

foreach ($old_id_to_new_id as $query_old => $id_new) {
    $post_title        = 'Unknown Post';
    $post_translations = [];

    foreach ($posts as $post) {
        if ($id_new === $post->getId()) {
            $post_title        = $post->getTitle();
            $post_translations = $post->getTranslations();

            break;
        }
    }

    echo \sprintf(
        '# %s<br>',
        $post_title
    );

    foreach ($languages as $language_code) {
        $post_id = $post_translations[$language_code];

        echo \sprintf(
            'RewriteCond %%{QUERY_STRING} ^%s&language=(%s) [OR]<br>',
            $query_old,
            $language_code
        );

        echo \sprintf(
            'RewriteCond %%{QUERY_STRING} ^language=(%s)&%s<br>',
            $language_code,
            $query_old
        );

        echo \sprintf(
            'RewriteRule ^blog\.php$ /blog_listing.php?language=$1&post=%d [R=301,L]<br>',
            $post_id
        );
    }

    /** Fallback without language parameter */
    echo \sprintf(
        'RewriteCond %%{QUERY_STRING} ^%s$<br>',
        $query_old
    );

    echo \sprintf(
        'RewriteRule ^blog\.php$ /blog_listing.php?post=%d [R=301,L]<br>',
        $id_new
    );
    /** */

    echo '<br>';
}

/**
 * Categories
 */
$categories_options = [
    'lang' => Blog::getLanguageCodeDefault(),
];
$categories         = Blog::getCategories($categories_options);

$old_category_id_to_new = [
    'blog_cat=2' => 22,
    'blog_cat=3' => 97,
];
foreach ($old_category_id_to_new as $query_old => $id_new) {
    $category_title        = 'Unknown Category';
    $category_translations = [];

    foreach ($categories as $category) {
        if ($id_new === $category->getId()) {
            $category_title        = $category->getName();
            $category_translations = $category->getTranslations();

            break;
        }
    }

    echo \sprintf(
        '# %s<br>',
        $category_title
    );

    foreach ($languages as $language_code) {
        $category_id = $category_translations[$language_code];

        echo \sprintf(
            'RewriteCond %%{QUERY_STRING} ^%s&language=(%s) [OR]<br>',
            $query_old,
            $language_code
        );

        echo \sprintf(
            'RewriteCond %%{QUERY_STRING} ^language=(%s)&%s<br>',
            $language_code,
            $query_old
        );

        echo \sprintf(
            'RewriteRule ^blog\.php$ /blog_listing.php?language=$1&category_id=%d [R=301,L]<br>',
            $category_id
        );
    }

    /** Fallback without language parameter */
    echo \sprintf(
        'RewriteCond %%{QUERY_STRING} ^%s$<br>',
        $query_old
    );

    echo \sprintf(
        'RewriteRule ^blog\.php$ /blog_listing.php?category_id=%d [R=301,L]<br>',
        $id_new
    );
    /** */

    echo '<br>';
}

/** Blog home */
echo 'Redirect 301 "/blog.php" "/blog_new.php"';
/** */

echo '</pre>';

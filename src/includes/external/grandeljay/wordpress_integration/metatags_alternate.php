<?php

namespace Grandeljay\WordpressIntegration;

if (!isset($key)) {
    return;
}

if (isset($post)) {
    $post_id           = $post->getId();
    $post_translations = $post->getTranslations();

    $_GET['post'] =  $post_translations[$key]
                  ?? $post_id;
}

if (isset($category)) {
    $category_id           = $category->getId();
    $category_translations = $category->getTranslations();

    $_GET['category_id'] =  $category_translations[$key]
                         ?? $category_id;
}

if (isset($tag)) {
    $tag_id           = $tag->getId();
    $tag_translations = $tag->getTranslations();

    $_GET['tag_id'] =  $tag_translations[$key]
                    ?? $tag_id;
}

$alternate_link = \xtc_href_link(
    \basename($PHP_SELF),
    \xtc_get_all_get_params_include(
        [
            'products_id',
            'cPath',
            'manufacturers_id',
            'coID',

            'blog_cat',
            'blog_item',

            'post',
            'tag_id',
            'category_id',
        ]
    )
    . 'language=' . $key . $page_param,
    'NONSSL',
    false
);

<?php

namespace Grandeljay\WordpressIntegration;

if (!isset($key)) {
    return;
}

$url_server             = \ENABLE_SSL ? \HTTPS_SERVER : \HTTP_SERVER;
$url_parameters_include = [
    'page',
    'products_id',
    'cPath',
    'manufacturers_id',
    'coID',

    'blog_cat',
    'blog_item',

    'search',
];
$url_alternate          = new Url($url_server . $PHP_SELF);
$url_alternate->addParameters(
    [
        'language' => $key,
    ]
);

foreach ($url_parameters_include as $parameter) {
    if (isset($_GET[$parameter])) {
        $url_alternate->addParameters([$parameter => $_GET[$parameter]]);
    }
}

if (isset($_GET['post'], $post)) {
    $post_id           = $post->getId();
    $post_translations = $post->getTranslations();
    $post_get          = $post_translations[$key]
                      ?? $post_id;

    $url_alternate->addParameters(['post' => $post_get]);
}

if (isset($_GET['category_id'], $category)) {
    $category_id           = $category->getId();
    $category_translations = $category->getTranslations();
    $category_get          = $category_translations[$key]
                          ?? $category_id;

    $url_alternate->addParameters(['category_id' => $category_get]);
}

if (isset($_GET['tag_id'], $tag_ids)) {
    $tags_requested = \array_filter(
        $tag_ids,
        /** Determine which tags are requested. */
        function (Tag $tag) {
            $tag_translations = $tag->getTranslations();

            foreach ($tag_translations as $translation_id) {
                if (\in_array($translation_id, $_GET['tag_id'])) {
                    return true;
                }
            }

            return false;
        }
    );

    $tags_get = [];

    foreach ($tags_requested as $tag) {
        $tag_id           = $tag->getId();
        $tag_translations = $tag->getTranslations();

        $tags_get[] =  $tag_translations[$key]
                    ?? $tag_id;
    }

    $url_alternate->addParameters(['tag_id' => $tags_get]);
}

$alternate_link = $url_alternate->toString();

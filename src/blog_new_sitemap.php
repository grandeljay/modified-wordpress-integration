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

$options         = [
    'lang'     => Blog::getLanguageCodeDefault(),
    'per_page' => 100,
    'page'     => 1,

    'orderby'  => 'date',
    'order'    => 'desc',
];
$posts_with_meta = Blog::getPosts($options, true);
$posts           = $posts_with_meta['posts'];

/**
 * As with all XML files, all tag values must be entity escaped. Google ignores
 * `<priority>` and `<changefreq>` values.
 *
 * @link https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap
 */
$sitemap_server     = \ENABLE_SSL ? \HTTPS_SERVER : \HTTP_SERVER;
$sitemap_stylesheet = $sitemap_server . '/templates/' . \CURRENT_TEMPLATE . '/css/grandeljay_wordpress_integration_blog_sitemap.css';
$sitemap_xml        = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="{$sitemap_stylesheet}"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" />
XML;

$blog_url_home = \htmlentities($sitemap_server . Constants::BLOG_URL_HOME, \ENT_QUOTES, 'UTF-8');

$sitemap          = new \SimpleXMLElement($sitemap_xml);
$sitemap_url_home = $sitemap->addChild('url');
$sitemap_url_home->addChild('loc', $blog_url_home);

/** Use most recent post date */
// $url->addChild('lastmod', date('c', \strtotime('2024-05-24 08:40:13')));

$languages_query = \xtc_db_query(
    \sprintf(
        'SELECT *
           FROM `%s`
          WHERE `status` = 1',
        \TABLE_LANGUAGES
    )
);
$languages_count = \xtc_db_num_rows($languages_query);

if ($languages_count > 1) {
    while ($language_data = \xtc_db_fetch_array($languages_query)) {
        $alternate_language_code = $language_data['code'];
        $alternate_parameters    = \http_build_query(['language' => $alternate_language_code]);
        $alternate_location      = \xtc_href_link(\ltrim(Constants::BLOG_URL_HOME, '/'), $alternate_parameters);
        $alternate_location      = \str_replace('&amp;', '&', $alternate_location);

        $alternate = $sitemap_url_home->addChild('xhtml:link', null, 'http://www.w3.org/1999/xhtml');
        $alternate->addAttribute('rel', 'alternate');
        $alternate->addAttribute('hreflang', $alternate_language_code);
        $alternate->addAttribute('href', $alternate_location);
    }
}

foreach ($posts as $post) {
    $post_original_id            = $post->getId();
    $post_original_link          = $post->getLink();
    $post_original_language      = $post->getLanguageCode();
    $post_original_translations  = $post->getTranslations();
    $post_original_date_modified = $post->getDateModified();

    $url_post = $sitemap->addChild('url');
    $url_post->addChild('loc', \htmlentities($post_original_link, \ENT_QUOTES, 'UTF-8'));
    $url_post->addChild('lastmod', date('c', $post_original_date_modified));

    foreach ($post_original_translations as $post_translation_language_code => $post_translation_id) {
        $post_translation_link = \str_replace(
            [
                \sprintf('language=%s', $post_original_language),
                \sprintf('post=%s', $post_original_id),
            ],
            [
                \sprintf('language=%s', $post_translation_language_code),
                \sprintf('post=%s', $post_translation_id),
            ],
            $post_original_link
        );

        $url_post_alternate = $url_post->addChild('xhtml:link', null, 'http://www.w3.org/1999/xhtml');
        $url_post_alternate->addAttribute('rel', 'alternate');
        $url_post_alternate->addAttribute('hreflang', $post_translation_language_code);
        $url_post_alternate->addAttribute('href', $post_translation_link);
    }
}

header('Content-Type: application/xml; charset=UTF-8');
$xml = $sitemap->asXML();

\file_put_contents(\DIR_FS_CATALOG . 'blog_new_sitemap.xml', $xml);

echo $xml;

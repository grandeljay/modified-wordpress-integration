<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

class Blog
{
    public static function getLanguageCodeDefault(): string
    {
        return \DEFAULT_LANGUAGE;
    }

    public static function getLanguageCode(): string
    {
        $language_code = $_GET['language']
                      ?? $_SESSION['language_code']
                      ?? self::getLanguageCodeDefault();

        return $language_code;
    }

    public static function getPage(int $id): Page
    {
        $endpoint = Constants::BLOG_URL_API_PAGES . $id;

        $url = new Url($endpoint);
        $url->addParameters(
            [
                '_embed' => true,
            ]
        );
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $page_wp = $url->getRequestBody();
        $page    = new Page($page_wp);

        return $page;
    }

    public static function getPost(int $id): Post
    {
        $endpoint = Constants::BLOG_URL_API_POSTS . $id;

        $url = new Url($endpoint);
        $url->addParameters(
            [
                '_embed' => true,
            ]
        );
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $post_wp = $url->getRequestBody();
        $post    = new Post($post_wp);

        return $post;
    }

    public static function getPosts(array $options = [], bool $get_all_posts = false): array
    {
        $endpoint = Constants::BLOG_URL_API_POSTS;

        $options_default = Post::getDefaultOptions();

        /**
         * Merge fields
         */
        $fields_default   = $options_default['_fields'] ?? [];
        $fields_requested = $options['_fields']         ?? [];

        foreach ($fields_requested as $field) {
            if (!\in_array($field, $fields_default)) {
                $fields_default[] = $field;
            }
        }

        /**
         * If the input arrays have the same string keys, then the later value
         * for that key will overwrite the previous one.
         *
         * @link https://www.php.net/manual/en/function.array-merge.php
         */
        $options            = \array_merge($options_default, $options);
        $options['_fields'] = $fields_default;

        $url = new Url($endpoint);
        $url->addParameters($options);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $posts_meta = $url->getRequestHeaders();
        $posts_wp   = $url->getRequestBody();
        $posts      = [];

        foreach ($posts_wp as $post_wp) {
            $post    = new Post($post_wp);
            $posts[] = $post;
        }

        $posts_total       = $posts_meta['x-wp-total'];
        $posts_total_pages = $posts_meta['x-wp-totalpages'];

        /**
         * `per_page` is capped at 100.
         *
         * @link https://developer.wordpress.org/rest-api/using-the-rest-api/pagination/
         */
        if (true === $get_all_posts && $posts_total > $options['per_page']) {
            for ($posts_page = 2; $posts_page <= $posts_total_pages; $posts_page++) {
                $options['page'] = $posts_page;

                $url->addParameters($options);
                $url->makeRequest();

                if (!$url->isRequestSuccessful()) {
                    continue;
                }

                $posts_wp = $url->getRequestBody();

                foreach ($posts_wp as $post_wp) {
                    $post    = new Post($post_wp);
                    $posts[] = $post;
                }
            }
        }

        $posts_with_meta = [
            'total'       => $posts_total,
            'total_pages' => $posts_total_pages,
            'posts'       => $posts,
            'page'        => $options['page'],
        ];

        return $posts_with_meta;
    }

    /**
     * Returns only the assigned tags of a post.
     *
     * @param  int   $post_id The post id.
     *
     * @return array
     */
    public static function getPostTags(int $post_id): array
    {
        $endpoint = \sprintf(Constants::BLOG_URL_API_TAGS_BY_POST, $post_id);

        $url = new Url($endpoint);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $tags_wp = $url->getRequestBody();
        $tags    = [];

        foreach ($tags_wp as $tag_wp) {
            $tag    = new Tag($tag_wp);
            $tags[] = $tag->toArray();
        }

        return $tags;
    }

    public static function getCategory(int $id): Category|null
    {
        $endpoint = Constants::BLOG_URL_API_CATEGORIES . $id;

        $url = new Url($endpoint);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return null;
        }

        $category_wp = $url->getRequestBody();
        $category    = new Category($category_wp);

        return $category;
    }

    /**
     * Returns all post categories via the WordPress REST API.
     *
     * @param  array $options Query parameters for the WordPress REST API.
     *
     * @return array An array containing all `Category` instances.
     *
     * @link https://developer.wordpress.org/rest-api/reference/categories/#arguments
     */
    public static function getCategories(array $options = []): array
    {
        $endpoint = Constants::BLOG_URL_API_CATEGORIES;

        $options_default = Category::getDefaultOptions();

        /**
         * Merge fields
         */
        $fields_default   = $options_default['_fields'] ?? [];
        $fields_requested = $options['_fields']         ?? [];

        foreach ($fields_requested as $field) {
            if (!\in_array($field, $fields_default)) {
                $fields_default[] = $field;
            }
        }

        /**
         * If the input arrays have the same string keys, then the later value
         * for that key will overwrite the previous one.
         *
         * @link https://www.php.net/manual/en/function.array-merge.php
         */
        $options            = \array_merge($options_default, $options);
        $options['_fields'] = $fields_default;

        $url = new Url($endpoint);
        $url->addParameters($options);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $categories_wp = $url->getRequestBody();
        $categories    = [];

        foreach ($categories_wp as $category_wp) {
            $category_id = $category_wp['id'];
            $category    = new Category($category_wp);

            $categories[$category_id] = $category;
        }

        return $categories;
    }

    public static function getTag(int $id): Tag|null
    {
        $endpoint = Constants::BLOG_URL_API_TAGS . $id;

        $url = new Url($endpoint);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return null;
        }

        $tag_wp = $url->getRequestBody();
        $tag    = new Tag($tag_wp);

        return $tag;
    }

    /**
     * Returns all post tags via the WordPress REST API.
     *
     * @param  array $options Query parameters for the WordPress REST API.
     *
     * @return array An array containing all `Tag` instances.
     *
     * @link https://developer.wordpress.org/rest-api/reference/tags/#arguments
     */
    public static function getTags(array $options = []): array
    {
        $endpoint = Constants::BLOG_URL_API_TAGS;

        $options_default = Tag::getDefaultOptions();

        /**
         * Merge fields
         */
        $fields_default   = $options_default['_fields'] ?? [];
        $fields_requested = $options['_fields']         ?? [];

        foreach ($fields_requested as $field) {
            if (!\in_array($field, $fields_default)) {
                $fields_default[] = $field;
            }
        }

        /**
         * If the input arrays have the same string keys, then the later value
         * for that key will overwrite the previous one.
         *
         * @link https://www.php.net/manual/en/function.array-merge.php
         */
        $options            = \array_merge($options_default, $options);
        $options['_fields'] = $fields_default;

        $url = new Url($endpoint);
        $url->addParameters($options);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $tags_wp = $url->getRequestBody();
        $tags    = [];

        foreach ($tags_wp as $tag_wp) {
            $tag_id = $tag_wp['id'];
            $tag    = new Tag($tag_wp);

            $tags[$tag_id] = $tag;
        }

        return $tags;
    }

    public static function getPaginationHtml(array $options, array $response_with_meta): string
    {
        $smarty = new \smarty();
        $smarty->assign('language', $_SESSION['language']);

        $options = \array_merge(
            Post::getDefaultOptions(),
            $options
        );

        /** Links */
        $posts             = $response_with_meta['posts'];
        $posts_pages_total = $response_with_meta['total_pages'];
        $posts_page_links  = self::getPageLinks($posts, $options, $posts_pages_total);

        /** Pagination */
        $per_page = \min($options['per_page'], $response_with_meta['total']);

        $offset_start = $per_page * ($options['page'] - 1) + 1;
        $offset_end   = \min($per_page * $options['page'], $response_with_meta['total']);

        $smarty->assign('pagination_offset_start', $offset_start);
        $smarty->assign('pagination_offset_end', $offset_end);
        $smarty->assign('pagination_posts_total', $response_with_meta['total']);

        $smarty->assign('posts_page', $response_with_meta['page']);
        $smarty->assign('posts_page_links', $posts_page_links);
        $smarty->assign('posts_pages_total', $posts_pages_total);

        /** HTML */
        $pagination_html = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/pagination.html');

        return $pagination_html;
    }

    public static function getModuleTranslations(): Translations
    {
        $module_language_filepath = \sprintf(
            '%s/modules/system/%s.php',
            \DIR_WS_LANGUAGES . $_SESSION['language'],
            \grandeljay_wordpress_integration::class
        );

        require $module_language_filepath;

        return $translations;
    }

    public static function getPageLinks(array $posts, array $options, int $posts_pages_total): array
    {
        $posts_page_links = [];

        for ($page = 1; $page <= $posts_pages_total; $page++) {
            $url_parameters = [
                'lang' => $options['lang'],
                'page' => $page,
            ];

            if (isset($options['categories'])) {
                $url_parameters['category_id'] = $options['categories'];
            }

            if (isset($options['tags'])) {
                $url_parameters['tag_id'] = \explode(',', $options['tags']);
            }

            $url = new Url(Constants::BLOG_URL_POSTS);
            $url->addParameters($url_parameters);

            $posts_page_links[$page] = $url->toString();
        }

        return $posts_page_links;
    }

    public static function getFilterHtml(\Smarty &$smarty, bool $with_categories = true, bool $with_tags = true): string
    {
        /** Filter categories */
        if ($with_categories) {
            $categories_options = [
                /** WordPress */
                '_fields' => 'name',
            ];
            $categories         = Blog::getCategories($categories_options);
            $categories         = \array_filter(
                $categories,
                function (Category $category) {
                    return !$category->isUncategorised();
                }
            );
            $categories_array   = \array_map(
                function (Category $category) {
                    return $category->toArray();
                },
                $categories
            );

            $smarty->assign('categories', $categories_array);

            if (!empty($_GET['category_id'])) {
                $smarty->assign('category_id', $_GET['category_id']);
            }
        }

        /** Filter tags */
        if ($with_tags) {
            $tags_options = [];
            $tags         = Blog::getTags($tags_options);
            $tags_array   = \array_map(
                function (Tag $tag) {
                    return $tag->toArray();
                },
                $tags
            );

            $smarty->assign('tags', $tags_array);
        }

        /** Get HTML */
        $translations           = Blog::getModuleTranslations();
        $form_action            = \xtc_href_link(\basename(Constants::BLOG_URL_POSTS));
        $form_input_placeholder = $translations->get('POSTS_SEARCH');
        $form_input_value       = $_GET['search'] ?? '';

        $smarty->assign('form_action', $form_action);
        $smarty->assign('form_input_placeholder', $form_input_placeholder);
        $smarty->assign('form_input_value', $form_input_value);

        $html_filter = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/filter.html');

        return $html_filter;
    }

    public static function getListingHtml(array $posts_with_meta, $options): string
    {
        global $smarty;

        /** Pagination */
        $html_pagination = self::getPaginationHtml($options, $posts_with_meta);
        $smarty->assign('pagination', $html_pagination);

        /** Posts */
        $posts       = $posts_with_meta['posts'];
        $posts_array = \array_map(
            function (Post $post) {
                return $post->toArray();
            },
            $posts
        );
        $smarty->assign('posts', $posts_array);

        /** Filter */
        $html_filter = self::getFilterHtml($smarty, true, true);
        $smarty->assign('filter', $html_filter);

        /** HTML */
        $html_listing = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/listing.html');

        return $html_listing;
    }

    public static function getFrontPage(): Page
    {
        $language_code = Blog::getLanguageCode();

        if (isset($_SESSION['grandeljay']['wordpress_integration']['front_page'][$language_code])) {
            return $_SESSION['grandeljay']['wordpress_integration']['front_page'][$language_code];
        }

        /**
         * Get the front page from WordPress
         *
         * It would be better to use the `/wp/v2/settings` endpoint to determine
         * which page is used as the home page (`page_on_front`).
         */
        $wp_page_front_id = match ($language_code) {
            'en'    => 1028,
            'es'    => 1030,
            'fr'    => 1024,
            'it'    => 1026,
            default => 952
        };
        $wp_page_front = Blog::getPage($wp_page_front_id);

        $_SESSION['grandeljay']['wordpress_integration']['front_page'][$language_code] = $wp_page_front;

        return $wp_page_front;
    }

    public static function getIntroductionHtml(): string
    {
        $page         = self::getFrontPage();
        $page_excerpt = $page->getExcerpt();
        $page_content = $page->getContent();

        $translations   = Blog::getModuleTranslations();
        $text_read_more = $translations->get('INTRODUCTION_LINK_READ_MORE');
        $text_read_less = $translations->get('INTRODUCTION_LINK_READ_LESS');

        \ob_start();
        ?>
        <section id="introduction">
            <div class="excerpt">
                <?= $page_excerpt ?>
            </div>

            <?php if ($page_content) { ?>
                <div id="read_more_content" class="content">
                    <?= $page_content ?>
                </div>

                <a id="read_more_link" class="hide">
                    <i class="fas fa-angle-double-down"></i>
                    <?= $text_read_more ?>
                    <i class="fas fa-angle-double-down"></i>
                </a>

                <a id="read_less_link" class="hide">
                    <i class="fas fa-angle-double-up"></i>
                    <?= $text_read_less ?>
                    <i class="fas fa-angle-double-up"></i>
                </a>
            <?php } ?>
        </section>
        <?php
        $introduction = \ob_get_clean();

        return $introduction;
    }
}

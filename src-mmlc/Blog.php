<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

class Blog
{
    /**
     * Get API Response
     *
     * @param  string $endpoint
     * @param  bool   $ignore_per_page To work around the `per_page` cap of 100,
     *                                 multiple requests will be made and
     *                                 combined before returning.
     *
     * @return array
     */
    private static function getApiResponse(string $endpoint, array $options, bool $ignore_per_page): array
    {
        /**
         * `per_page` defaults to 10.
         *
         * @link https://developer.wordpress.org/rest-api/reference/posts/
         */
        if (!isset($options['per_page'])) {
            $options['per_page'] = 10;
        }

        $url = new Url($endpoint);
        $url->addParameters($options);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $response_headers = $url->getRequestHeaders();
        $response_body    = $url->getRequestBody();
        $response         = [
            'headers' => $response_headers,
            'body'    => $response_body,
        ];

        if (!isset($response_headers['x-wp-total'], $response_headers['x-wp-totalpages'])) {
            return $response;
        }

        $posts_total       = $response_headers['x-wp-total'];
        $posts_total_pages = $response_headers['x-wp-totalpages'];

        /**
         * `per_page` is capped at 100.
         *
         * @link https://developer.wordpress.org/rest-api/using-the-rest-api/pagination/
         */
        if (true === $ignore_per_page && $posts_total > $options['per_page']) {
            for ($posts_page = 2; $posts_page <= $posts_total_pages; $posts_page++) {
                $options['page'] = $posts_page;

                $response_to_combine = self::getApiResponse($endpoint, $options, false);

                if (empty($response_to_combine)) {
                    continue;
                }

                $response['body'] += $response_to_combine['body'];
            }
        }

        return $response;
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
         * If the input arrays have the same string keys, then the later value
         * for that key will overwrite the previous one.
         *
         * @link https://www.php.net/manual/en/function.array-merge.php
         */
        $options = \array_merge($options_default, $options);

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
         * If the input arrays have the same string keys, then the later value
         * for that key will overwrite the previous one.
         *
         * @link https://www.php.net/manual/en/function.array-merge.php
         */
        $options = \array_merge($options_default, $options);

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
         * If the input arrays have the same string keys, then the later value
         * for that key will overwrite the previous one.
         *
         * @link https://www.php.net/manual/en/function.array-merge.php
         */
        $options = \array_merge($options_default, $options);

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
                $url_parameters['tag_id'] = $options['tags'];
            }

            $url = new Url(Constants::BLOG_URL_POSTS);
            $url->addParameters($url_parameters);

            $posts_page_links[$page] = $url->toString();
        }

        return $posts_page_links;
    }

    public static function getCategoryTags(array $posts, bool $as_array = false): array
    {
        $category_tags = [];

        foreach ($posts as $post) {
            $post_tags = $post->getTags();

            if (empty($post_tags)) {
                continue;
            }

            foreach ($post_tags as $tag) {
                $tag_id = $tag->getId();

                if (isset($category_tags[$tag_id])) {
                    continue;
                }

                $category_tags[$tag_id] = $tag;
            }
        }

        if (!$as_array) {
            return $category_tags;
        }

        $category_tags_array = \array_map(
            function (Tag $tag) {
                return $tag->toArray();
            },
            $category_tags
        );

        return $category_tags_array;
    }

    public static function getFilterHtml(): string
    {
        global $smarty, $tags;

        $filter_tags = \array_map(
            function (Tag $tag) {
                return $tag->toArray();
            },
            $tags
        );

        $smarty->assign('filter_tags', $filter_tags);

        /** Filter reset */
        $filter_reset_parameters = $_GET;

        unset($filter_reset_parameters['page']);
        unset($filter_reset_parameters['category_id']);
        unset($filter_reset_parameters['tag_id']);

        $filter_reset_server = \ENABLE_SSL ? \HTTPS_SERVER : \HTTP_SERVER;
        $filter_reset_link   = new Url($filter_reset_server . Constants::BLOG_URL_POSTS);
        $filter_reset_link->addParameters($filter_reset_parameters);
        $smarty->assign('filter_reset_link', $filter_reset_link->toString());

        /** Filter categories */
        global $categories;

        $categories_array = \array_map(
            function (Category $category) {
                return $category->toArray();
            },
            $categories
        );

        $smarty->assign('categories', $categories_array);

        /** Get HTML */
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
        $html_filter = self::getFilterHtml();
        $smarty->assign('filter', $html_filter);

        /** HTML */
        $html_listing = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/listing.html');

        return $html_listing;
    }
}

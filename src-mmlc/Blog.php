<?php

namespace Grandeljay\WordpressIntegration;

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

    public static function getPost(int $id): array
    {
        $endpoint = Constants::BLOG_URL_API_POSTS . $id;

        $url = new Url($endpoint);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $categories_options = [
            'lang' => $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE,
        ];
        $categories         = self::getCategories($categories_options);

        $tags_options = [
            'lang' => $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE,
        ];
        $tags         = self::getTags($tags_options);

        $post_wp = $url->getRequestBody();
        $post    = new Post($post_wp);
        $post->setCategories($categories);
        $post->setTags($tags);

        if ($post->getLanguage() !== $_SESSION['language_code']) {
            return [];
        }

        return $post->toArray();
    }

    public static function getPosts(array $options, bool $get_all_posts = false): array
    {
        $endpoint = Constants::BLOG_URL_API_POSTS;

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

        $posts_meta = $url->getRequestHeaders();
        $posts_wp   = $url->getRequestBody();
        $posts      = [];

        $categories_options = [
            'lang' => $options['lang'],
        ];
        $categories         = self::getCategories($categories_options);

        $tags_options = [
            'lang' => $options['lang'],
        ];
        $tags         = self::getTags($tags_options);

        foreach ($posts_wp as $post_wp) {
            $post = new Post($post_wp);
            $post->setCategories($categories);
            $post->setTags($tags);

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
                    $post = new Post($post_wp);
                    $post->setCategories($categories);
                    $post->setTags($tags);

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

    public static function getPostsSearch(array $options, bool $get_all_posts = false): array
    {
        $endpoint = Constants::BLOG_URL_API_SEARCH;
        $response = self::getApiResponse($endpoint, $options, $get_all_posts);

        if (empty($response)) {
            return [];
        }

        $search_results_meta = $response['headers'];
        $search_results_wp   = $response['body'];
        $search_results      = [];

        foreach ($search_results_wp as $search_result_wp) {
            $search_result = new SearchResult($search_result_wp);

            $search_results[] = $search_result;
        }

        $search_results_total       = $search_results_meta['x-wp-total'];
        $search_results_total_pages = $search_results_meta['x-wp-totalpages'];

        $search_results_with_meta = [
            'total'          => $search_results_total,
            'total_pages'    => $search_results_total_pages,
            'search_results' => $search_results,
            'page'           => $options['page'],
        ];

        return $search_results_with_meta;
    }

    public static function getPostsHtml(array $options): string
    {
        $posts_html = '';

        $posts_with_meta   = self::getPosts($options);
        $posts             = \array_map(
            function (Post $post) {
                return $post->toArray();
            },
            $posts_with_meta['posts']
        );
        $posts_page        = $posts_with_meta['page'];
        $posts_page_links  = [];
        $posts_pages       = $posts_with_meta['total'];
        $posts_pages_total = $posts_with_meta['total_pages'];

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
            $url->addParameters(
                [
                    'lang' => $options['lang'],
                    'page' => $page,
                ]
            );

            $posts_page_links[$page] = $url->toString();
        }

        global $smarty, $breadcrumb;

        if (empty($posts)) {
            \http_response_code(404);

            $breadcrumb->add('404', '#');

            $posts_html = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/listing_empty.html');
        } else {
            /** Pagination */
            $per_page = \min($options['per_page'], $posts_with_meta['total']);
            $offset   = $per_page * ($options['page'] - 1) + 1;
            $page     = $options['page'];

            $smarty->assign('pagination_offset_start', $offset);
            $smarty->assign('pagination_offset_end', \min($per_page * $page, $posts_with_meta['total']));
            $smarty->assign('pagination_posts_total', $posts_pages);

            $smarty->assign('posts_page', $posts_page);
            $smarty->assign('posts_page_links', $posts_page_links);
            $smarty->assign('posts_pages_total', $posts_pages_total);

            $pagination = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/pagination.html');
            $smarty->assign('pagination', $pagination);

            /** Posts */
            $smarty->assign('posts', $posts);

            /** Taxonomies */
            if (isset($_GET['category_id']) || isset($_GET['tag_id'])) {
                if (isset($_GET['category_id'])) {
                    $category = self::getCategory($_GET['category_id']);
                    $smarty->assign('filter_category', $category->toArray());
                }

                if (isset($_GET['tag_id'])) {
                    $tag = self::getTag($_GET['tag_id']);
                    $smarty->assign('filter_tag', $tag->toArray());
                }

                /** Filter reset */
                $filter_reset_parameters = $_GET;

                if (isset($filter_reset_parameters['category_id'])) {
                    unset($filter_reset_parameters['category_id']);
                }

                if (isset($filter_reset_parameters['tag_id'])) {
                    unset($filter_reset_parameters['tag_id']);
                }

                $filter_reset_server = \ENABLE_SSL ? \HTTPS_SERVER : \HTTP_SERVER;
                $filter_reset_link   = new Url($filter_reset_server . Constants::BLOG_URL_POSTS);
                $filter_reset_link->addParameters($filter_reset_parameters);
                $smarty->assign('filter_reset_link', $filter_reset_link->toString());
            }

            $posts_html = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/listing.html');
        }

        return $posts_html;
    }

    public static function getPostsSearchHtml(array $options): string
    {
        $search_results_with_meta   = self::getPostsSearch($options);
        $search_results             = \array_map(
            function (SearchResult $search_results) {
                return $search_results->toArray();
            },
            $search_results_with_meta['search_results']
        );
        $search_results_pages_total = $search_results_with_meta['total_pages'];
        $search_results_page_links  = [];

        for ($page = 1; $page <= $search_results_pages_total; $page++) {
            $url = new Url(Constants::BLOG_URL_POSTS);
            $url->addParameters(
                [
                    'lang'   => $options['lang'],
                    'page'   => $page,
                    'search' => $options['search'],
                ]
            );

            $search_results_page_links[$page] = $url->toString();
        }

        global $smarty, $breadcrumb;

        require \sprintf(
            '%s/modules/system/%s.php',
            \DIR_WS_LANGUAGES . $_SESSION['language'],
            \grandeljay_wordpress_integration::class
        );

        $search_title       = $translations->get('FORM_SEARCH_TITLE');
        $search_description = \sprintf(
            $translations->get('FORM_SEARCH_DESCRIPTION'),
            \sprintf('<strong>%s</strong>', $options['search'])
        );

        $smarty->assign('search_title', $search_title);
        $smarty->assign('search_description', $search_description);

        /** Pagination */
        $pagination_html = self::getPaginationHtml(
            $options,
            $search_results_with_meta,
            $search_results_page_links
        );

        $smarty->assign('pagination', $pagination_html);

        /** Search Results */
        require \DIR_FS_CATALOG . 'templates/' . \CURRENT_TEMPLATE . '/source/boxes/grandeljay_wordpress_integration_blog_posts_search.php';

        $smarty->assign('search_results', $search_results);

        $search_results_html = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/search_result/listing.html');

        return $search_results_html;
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

    public static function getCategories(array $options): array
    {
        $endpoint = Constants::BLOG_URL_API_CATEGORIES;

        $url = new Url($endpoint);
        $url->addParameters($options);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $categories_wp = $url->getRequestBody();
        $categories    = [];

        foreach ($categories_wp as $category_wp) {
            $category    = new Category($category_wp);
            $category_id = $category_wp['id'];

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

    public static function getTags(array $options): array
    {
        $endpoint = Constants::BLOG_URL_API_TAGS;

        $url = new Url($endpoint);
        $url->addParameters($options);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $tags_wp = $url->getRequestBody();
        $tags    = [];

        foreach ($tags_wp as $tag_wp) {
            $tag    = new Tag($tag_wp);
            $tag_id = $tag_wp['id'];

            $tags[$tag_id] = $tag;
        }

        return $tags;
    }

    private static function getPaginationHtml(array $options, array $response_with_meta, array $links): string
    {
        global $smarty;

        $per_page = \min($options['per_page'], $response_with_meta['total']);

        $offset_start = $per_page * ($options['page'] - 1) + 1;
        $offset_end   = \min($per_page * $options['page'], $response_with_meta['total']);

        $smarty->assign('pagination_offset_start', $offset_start);
        $smarty->assign('pagination_offset_end', $offset_end);
        $smarty->assign('pagination_posts_total', $response_with_meta['total']);

        $smarty->assign('posts_page', $response_with_meta['page']);
        $smarty->assign('posts_page_links', $links);
        $smarty->assign('posts_pages_total', $response_with_meta['total_pages']);

        $pagination_html = $smarty->fetch(\CURRENT_TEMPLATE . '/module/grandeljay_wordpress_integration/blog/post/pagination.html');

        return $pagination_html;
    }
}

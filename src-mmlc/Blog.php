<?php

namespace Grandeljay\WordpressIntegration;

class Blog
{
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

    public static function getPosts(array $options): array
    {
        $endpoint = Constants::BLOG_URL_API_POSTS;

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
        if ($posts_total > 100) {
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

        for ($i = 1; $i <= $posts_pages_total; $i++) {
            $url = new Url(Constants::BLOG_URL_POSTS);
            $url->addParameters(
                [
                    'options' => $options['lang'],
                    'page'    => $i,
                ]
            );

            $posts_page_links[$i] = $url->toString();
        }

        global $smarty, $breadcrumb;

        if (empty($posts)) {
            \http_response_code(404);

            $breadcrumb->add('404', '#');

            $posts_html = $smarty->fetch(CURRENT_TEMPLATE . '/module/blog_posts_not_found.html');
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

            $pagination = $smarty->fetch(CURRENT_TEMPLATE . '/module/blog_post_pagination.html');
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

            $posts_html = $smarty->fetch(CURRENT_TEMPLATE . '/module/blog_post_listing.html');
        }

        return $posts_html;
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
}

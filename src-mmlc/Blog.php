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

            $posts[] = $post->toArray();
        }

        $posts_total       = $posts_meta['x-wp-total'];
        $posts_total_pages = $posts_meta['x-wp-totalpages'];

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
        $posts             = $posts_with_meta['posts'];
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

            $smarty->assign('posts', $posts);
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

    public static function getFeaturedImage(int $id): array
    {
        $endpoint = Constants::BLOG_URL_API_MEDIA . $id;

        $url = new Url($endpoint);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $media_wp = $url->getRequestBody();
        $media    = new Media($media_wp);

        return $media->toArray();
    }

    public static function getCategory(int $id): array
    {
        $endpoint = Constants::BLOG_URL_API_CATEGORIES . $id;

        $url = new Url($endpoint);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return [];
        }

        $category_wp = $url->getRequestBody();
        $category    = new Category($category_wp);

        return $category->toArray();
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

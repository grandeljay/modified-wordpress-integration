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
            echo $url->getRequestHeadersDebug();

            return [];
        }

        $post_wp = $url->getRequestBody();
        $post    = new Post($post_wp);

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
            echo $url->getRequestHeadersDebug();

            return [];
        }

        $posts_meta = $url->getRequestHeaders();
        $posts_wp   = $url->getRequestBody();
        $posts      = [];

        foreach ($posts_wp as $post_wp) {
            $post = new Post($post_wp);

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

    public static function getFeaturedImage(int $id): array
    {
        $endpoint = Constants::BLOG_URL_API_MEDIA . $id;

        $url = new Url($endpoint);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            echo $url->getRequestHeadersDebug();

            return [];
        }

        $media_wp = $url->getRequestBody();
        $media    = new Media($media_wp);

        return $media->toArray();
    }

    public static function getCategory(int $id): array
    {
        $endpoint = Constants::BLOG_URL_API_CATEGORY . $id;

        $url = new Url($endpoint);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            echo $url->getRequestHeadersDebug();

            return [];
        }

        $category_wp = $url->getRequestBody();
        $category    = new Category($category_wp);

        return $category->toArray();
    }
}

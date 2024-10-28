<?php

namespace Grandeljay\WordpressIntegration;

class Constants
{
    public const MODULE_NAME = 'MODULE_SYSTEM_GRANDELJAY_WORDPRESS_INTEGRATION';

    public const BLOG_URL_HOME  = '/blog_new.php';
    public const BLOG_URL_POSTS = '/blog_listing.php';

    public const BLOG_URL_API_BASE         = 'https://blog.hybrid.supply/?rest_route=/';
    public const BLOG_URL_API_POSTS        = self::BLOG_URL_API_BASE . 'wp/v2/posts/';
    public const BLOG_URL_API_MEDIA        = self::BLOG_URL_API_BASE . 'wp/v2/media/';
    public const BLOG_URL_API_CATEGORIES   = self::BLOG_URL_API_BASE . 'wp/v2/categories/';
    public const BLOG_URL_API_TAGS         = self::BLOG_URL_API_BASE . 'wp/v2/tags/';
    public const BLOG_URL_API_TAGS_BY_POST = self::BLOG_URL_API_BASE . 'wp/v2/tags&post=%d';
    public const BLOG_URL_API_POSTS_BY_TAG = self::BLOG_URL_API_BASE . 'wp/v2/post&tags=%d';
    public const BLOG_URL_API_SEARCH       = self::BLOG_URL_API_BASE . 'wp/v2/search/';
}

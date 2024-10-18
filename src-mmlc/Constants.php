<?php

namespace Grandeljay\WordpressIntegration;

class Constants
{
    public const MODULE_NAME = 'MODULE_SYSTEM_GRANDELJAY_WORDPRESS_INTEGRATION';

    public const BLOG_URL_HOME  = '/blog_new.php';
    public const BLOG_URL_POSTS = '/blog_listing.php';

    public const BLOG_URL_API_BASE     = 'https://blog.hybrid.supply/?rest_route=/';
    public const BLOG_URL_API_POSTS    = self::BLOG_URL_API_BASE . 'wp/v2/posts/';
    public const BLOG_URL_API_MEDIA    = self::BLOG_URL_API_BASE . 'wp/v2/media/';
    public const BLOG_URL_API_CATEGORY = self::BLOG_URL_API_BASE . 'wp/v2/categories/';
}

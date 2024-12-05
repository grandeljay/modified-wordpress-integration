<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_NAME);
$translations->add('TITLE', 'grandeljay - WordPress Integration');
$translations->add('TEXT_TITLE', 'WordPress Integration');

$translations->add('TITLE_BLOG', 'News');
$translations->add('TITLE_BLOG_LISTING', 'All');

$translations->add('INTRODUCTION_LINK_READ_MORE', 'Show more');
$translations->add('INTRODUCTION_LINK_READ_LESS', 'Show less');

$translations->add('POSTS', 'Posts');

$translations->add('BUTTON_POSTS_VIEW_ALL', 'View all posts');

$translations->add('POSTS_SEARCH_TITLE', 'Search');
$translations->add('POSTS_SEARCH', 'Search for posts');
$translations->add('POSTS_SEARCH_SUBMIT', 'Get results');

$translations->define();

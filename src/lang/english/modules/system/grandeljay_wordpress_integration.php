<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_NAME);
$translations->add('TITLE', 'grandeljay - WordPress Integration');
$translations->add('TEXT_TITLE', 'WordPress Integration');

$translations->add('BLOG', 'Blog');
$translations->add('POSTS', 'Posts');

$translations->add('BUTTON_POSTS_VIEW_ALL', 'View all posts');

$translations->add('POSTS_SEARCH_TITLE', 'Search');
$translations->add('POSTS_SEARCH', 'Search for posts');
$translations->add('POSTS_SEARCH_SUBMIT', 'Get results');

$translations->add('FORM_SEARCH_TITLE', 'Search results');
$translations->add('FORM_SEARCH_DESCRIPTION', 'Your query for %s has returned the following results:');
$translations->add('FORM_SEARCH_RESET', 'Reset search');

$translations->define();

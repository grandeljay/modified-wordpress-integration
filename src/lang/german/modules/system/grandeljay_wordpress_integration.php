<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_NAME);
$translations->add('TITLE', 'grandeljay - WordPress Integration');
$translations->add('TEXT_TITLE', 'WordPress Integration');

$translations->add('TITLE_BLOG_LISTING', 'Alle');

$translations->add('INTRODUCTION_LINK_READ_MORE', 'Mehr zeigen');
$translations->add('INTRODUCTION_LINK_READ_LESS', 'Weniger zeigen');

$translations->add('POSTS', 'Artikel');

$translations->add('BUTTON_POSTS_VIEW_ALL', 'Alle Artikel ansehen');

$translations->define();

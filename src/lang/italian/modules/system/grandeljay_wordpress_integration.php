<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_NAME);
$translations->add('TITLE', 'grandeljay - WordPress Integration');
$translations->add('TEXT_TITLE', 'WordPress Integration');

$translations->add('TITLE_BLOG_LISTING', 'Tutti');

$translations->add('INTRODUCTION_LINK_READ_MORE', 'Mostra di più');
$translations->add('INTRODUCTION_LINK_READ_LESS', 'Mostra meno');

$translations->add('POSTS', 'Messaggi');

$translations->add('BUTTON_POSTS_VIEW_ALL', 'Visualizza tutti i messaggi');

$translations->define();

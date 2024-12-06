<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_NAME);
$translations->add('TITLE', 'grandeljay - WordPress Integration');
$translations->add('TEXT_TITLE', 'WordPress Integration');

$translations->add('TITLE_BLOG_LISTING', 'Tous');

$translations->add('INTRODUCTION_LINK_READ_MORE', 'Afficher plus');
$translations->add('INTRODUCTION_LINK_READ_LESS', 'Montrer moins');

$translations->add('POSTS', 'Postes');

$translations->add('BUTTON_POSTS_VIEW_ALL', 'Voir tous les messages');

$translations->define();

<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_NAME);
$translations->add('TITLE', 'grandeljay - WordPress Integration');
$translations->add('TEXT_TITLE', 'WordPress Integration');

$translations->add('TITLE_BLOG_LISTING', 'Todos');

$translations->add('INTRODUCTION_LINK_READ_MORE', 'Mostrar más');
$translations->add('INTRODUCTION_LINK_READ_LESS', 'Mostrar menos');

$translations->add('POSTS', 'Puestos');

$translations->add('BUTTON_POSTS_VIEW_ALL', 'Ver todas las entradas');

$translations->define();

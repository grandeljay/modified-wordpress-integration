<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_NAME);
$translations->add('TITLE', 'grandeljay - WordPress Integration');
$translations->add('TEXT_TITLE', 'WordPress Integration');

$translations->add('BLOG', 'Blog');
$translations->add('POSTS', 'Beiträge');

$translations->add('BUTTON_POSTS_VIEW_ALL', 'Alle Beiträge ansehen');

$translations->add('POSTS_SEARCH_TITLE', 'Suche');
$translations->add('POSTS_SEARCH', 'Suche nach Beiträge');
$translations->add('POSTS_SEARCH_SUBMIT', 'Ergebnisse suchen');

$translations->add('FORM_SEARCH_TITLE', 'Suchergebnisse');
$translations->add('FORM_SEARCH_DESCRIPTION', 'Ihre Suche nach %s hat die folgenden Ergebnisse geliefert:');
$translations->add('FORM_SEARCH_RESET', 'Suche zurücksetzen');

$translations->define();

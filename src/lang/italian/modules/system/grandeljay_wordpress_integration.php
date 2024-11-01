<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_NAME);
$translations->add('TITLE', 'grandeljay - WordPress Integration');
$translations->add('TEXT_TITLE', 'WordPress Integration');

$translations->add('BLOG', 'News');
$translations->add('POSTS', 'Messaggi');

$translations->add('BUTTON_POSTS_VIEW_ALL', 'Visualizza tutti i messaggi');

$translations->add('POSTS_SEARCH_TITLE', 'Ricerca');
$translations->add('POSTS_SEARCH', 'Ricerca dei messaggi');
$translations->add('POSTS_SEARCH_SUBMIT', 'Ottenere risultati');

$translations->add('FORM_SEARCH_TITLE', 'Risultati della ricerca');
$translations->add('FORM_SEARCH_DESCRIPTION', 'La vostra ricerca di %s ha prodotto i seguenti risultati:');
$translations->add('FORM_SEARCH_RESET', 'Azzeramento della ricerca');

$translations->define();

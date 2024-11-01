<?php

namespace Grandeljay\WordpressIntegration;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_NAME);
$translations->add('TITLE', 'grandeljay - WordPress Integration');
$translations->add('TEXT_TITLE', 'WordPress Integration');

$translations->add('BLOG', 'News');
$translations->add('POSTS', 'Postes');

$translations->add('BUTTON_POSTS_VIEW_ALL', 'Voir tous les messages');

$translations->add('POSTS_SEARCH_TITLE', 'Recherche');
$translations->add('POSTS_SEARCH', 'Recherche de postes');
$translations->add('POSTS_SEARCH_SUBMIT', 'Obtenir des résultats');

$translations->add('FORM_SEARCH_TITLE', 'Résultats de la recherche');
$translations->add('FORM_SEARCH_DESCRIPTION', 'Votre requête pour %s a retourné les résultats suivants :');
$translations->add('FORM_SEARCH_RESET', 'Réinitialiser la recherche');

$translations->define();

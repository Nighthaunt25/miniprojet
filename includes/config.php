<?php
// =============================================
// CONFIGURATION GÉNÉRALE
// =============================================
define('BASE_URL', 'https://rss-agre.alwaysdata.net/');
define('SITE_NAME', 'Le Monde – Aggrégateur RSS');

// Fichiers CSV
define('FILE_USERS',    __DIR__ . '/../data/users.csv');
define('FILE_TOKENS',   __DIR__ . '/../data/tokens.csv');

// Email (à adapter avec votre serveur SMTP)
define('MAIL_FROM',    'noreply@lemonde-rss.fr');
define('MAIL_NAME',    'Le Monde RSS');

// Flux RSS disponibles (catégorie => URL)
define('RSS_FEEDS', [
    'À la une'          => 'https://www.lemonde.fr/rss/une.xml',
    'International'     => 'https://www.lemonde.fr/international/rss_full.xml',
    'France'            => 'https://www.lemonde.fr/france/rss_full.xml',
    'Politique'         => 'https://www.lemonde.fr/politique/rss_full.xml',
    'Économie'          => 'https://www.lemonde.fr/economie/rss_full.xml',
    'Société'           => 'https://www.lemonde.fr/societe/rss_full.xml',
    'Culture'           => 'https://www.lemonde.fr/culture/rss_full.xml',
    'Technologies'      => 'https://www.lemonde.fr/technologies/rss_full.xml',
    'Science'           => 'https://www.lemonde.fr/sciences/rss_full.xml',
    'Sport'             => 'https://www.lemonde.fr/sport/rss_full.xml',
    'Éducation'         => 'https://www.lemonde.fr/education/rss_full.xml',
    'Environnement'     => 'https://www.lemonde.fr/planete/rss_full.xml',
]);

session_start();

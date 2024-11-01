<?php \Netjog\Utils\Security::enforceWPKernel(); ?>
<?php

$urls = [
    '/virtualjog/authorize' => [
        'controller' => 'Netjog\\Controllers\\SecurityController',
        'action' => 'authorize',
        'role' => 'admin',
    ],
    '/virtualjog/logout' => [
        'controller' => 'Netjog\\Controllers\\SecurityController',
        'action' => 'logout',
        'role' => 'admin',
    ],
    '/virtualjog/document-insert' => [
        'controller' => 'Netjog\\Controllers\\DocumentController',
        'action' => 'insertDocument',
        'role' => 'admin',
    ],
    '/virtualjog/enable-cookie-module' => [
        'controller' => 'Netjog\\Controllers\\CookieController',
        'action' => 'enableCookieModule',
        'role' => 'admin',
    ],
    '/virtualjog/disable-cookie-module' => [
        'controller' => 'Netjog\\Controllers\\CookieController',
        'action' => 'disableCookieModule',
        'role' => 'admin',
    ],
    '/virtualjog/allow-providers' => [
        'controller' => 'Netjog\\Controllers\\CookieController',
        'action' => 'allowProviders',
        'role' => 'admin',
    ],
];

$assets = [
    'images' => [
        'header-bg' =>\Netjog\Virtualjog::$pluginWebPath . 'Resources/images/header-bg.png',
        'admin-menu-icon' => \Netjog\Virtualjog::$pluginWebPath . 'Resources/images/vjog-admin-menu-icon.png',
    ]
];

$config = [
    'urls' => $urls,
    'assets' => $assets,
];

return $config;
<?php

use App\Hook\ContentRouteHook;

return [
    "plugins" => [
        [
            'name' => 'pixl-cms',
            'install_method' => 'composer',
            'enabled' => true,
            'config' => require_once('vendor/pixlmint/pixl-cms/config/config.php'),
        ],
        [
            'name' => 'pixlcms-media-plugin',
            'install_method' => 'composer',
            'enabled' => true,
            'config' => require_once('vendor/pixlmint/pixlcms-media-plugin/config/config.php'),
        ],
    ],
    'routes' => [
        [
            'route' => '/',
            'controller' => App\Controller\IndexController::class,
            'function' => 'index',
        ]
    ],
    'hooks' => [
        [
            'anchor' => 'pre_find_route',
            'hook' => ContentRouteHook::class,
        ],
    ],
    'base' => [
        'debugEnabled' => false,
    ]
];

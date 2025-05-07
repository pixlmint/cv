<?php

require __DIR__ . '/vendor/autoload.php';

use PixlMint\CMS\CmsCore;

$core = new CmsCore([
    'twigTemplatePath' => $_SERVER['DOCUMENT_ROOT'] . '/templates',
]);
$core->init();

<?php

$sMetadataVersion = '1.1';
$aModule = [
    'id' => 'mo_etracker',
    'title' => 'etracker',
    'description' => [
        'de' => '<p><a href="##handbook-url##" target="_blank">Handbuch</a></p>',
        'en' => '<p><a href="##handbook-url##" target="_blank">Handbook</a></p>',
    ],
    'lang' => 'en',
    'thumbnail' => 'logo.png',
    'version' => '3.0.0 ##revision##',
    'author' => 'derksen mediaopt GmbH',
    'url' => 'https://www.mediaopt.de',
    'email' => 'support@mediaopt.de',
    'extend' => [],
    'files' => [],
    'blocks' => [],
    'settings' => [],
    'templates' => [],
    'events' => [
        'onActivate' => 'mo_etracker__install::onActivate',
        'onDeactivate' => 'mo_etracker__install::onDeactivate',
    ],
];

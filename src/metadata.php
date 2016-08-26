<?php

$sMetadataVersion = '1.1';
$aModule = [
    'id' => 'mo_etracker',
    'title' => 'mediaopt etracker Webcontrolling',
    'description' => [
        'de' => '<p>Erweitern Sie Ihren Shop um etracker Webcontrolling.</p>'
            . '<p><a href="##handbook-url##" target="_blank">Handbuch</a></p>',
        'en' => '<p>Add etracker features to your OXID shop.</p>'
            . '<p><a href="##handbook-url##" target="_blank">Handbook</a></p>',
    ],
    'lang' => 'en',
    'thumbnail' => 'logo.png',
    'version' => '2.1.0 ##revision##',
    'author' => 'derksen mediaopt GmbH',
    'url' => 'https://www.mediaopt.de',
    'email' => 'support@mediaopt.de',
    'extend' => [
        'oxorder' => 'mo/etracker/core/mo_etracker__oxorder',
        'oxviewconfig' => 'mo/etracker/core/mo_etracker__oxviewconfig',
    ],
    'files' => [
        'mo_etracker__main' => 'mo/etracker/classes/mo_etracker__main.php',
        'mo_etracker__install' => 'mo/etracker/classes/mo_etracker__install.php',
        'mo_etracker__helper' => 'mo/etracker/classes/mo_etracker__helper.php'
    ],
    'blocks' => [
        [
            'template' => 'layout/footer.tpl',
            'block' => 'footer_main',
            'file' => 'views/blocks/layout/footer/footer_main.tpl'
        ],
        [
            'template' => 'layout/base.tpl',
            'block' => 'head_css',
            'file' => 'views/blocks/layout/base/head_css.tpl'
        ],
    ],
    'settings' => [
        ['group' => 'mo_etracker__config', 'name' => 'moetsecurecode', 'type' => 'str', 'value' => ''],
        ['group' => 'mo_etracker__config', 'name' => 'moetsecurekey', 'type' => 'str', 'value' => ''],
        ['group' => 'mo_etracker__config', 'name' => 'moetsechannel', 'type' => 'str', 'value' => ''],
        ['group' => 'mo_etracker__config', 'name' => 'moetroot', 'type' => 'str', 'value' => ''],
        ['group' => 'mo_etracker__config', 'name' => 'moetdebug', 'type' => 'bool', 'value' => false],
    ],
    'templates' => [],
    'events' => [
        'onActivate' => 'mo_etracker__install::onActivate',
        'onDeactivate' => 'mo_etracker__install::onDeactivate',
    ],
];

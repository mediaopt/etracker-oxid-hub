<?php

$sMetadataVersion = '1.1';
$aModule = [
    'id' => 'mo_etracker',
    'title' => 'mediaopt etracker Webcontrolling',
    'description' => [
        'de' => '<p>Erweitern Sie Ihren Shop um etracker Webcontrolling.</p>'
            . '<p><a href="https://projects.mediaopt.de/projects/mopt-twk/wiki/Dokumentation" target="_blank">Handbuch</a></p>',
        'en' => '<p>Add etracker features to your OXID shop.</p>'
            . '<p><a href="https://projects.mediaopt.de/projects/mopt-twk/wiki/Dokumentation" target="_blank">Handbook</a></p>',
    ],
    'lang' => 'en',
    'thumbnail' => 'logo.png',
    'version' => '2.1.4 ##revision##',
    'author' => 'derksen mediaopt GmbH',
    'url' => 'https://www.mediaopt.de',
    'email' => 'support@mediaopt.de',
    'extend' => [
        'oxorder' => 'mo/etracker/core/mo_etracker__oxorder',
        'oxbasket' => 'mo/etracker/core/mo_etracker__oxbasket',
        'oxviewconfig' => 'mo/etracker/core/mo_etracker__oxviewconfig',
        'details' => 'mo/etracker/controllers/mo_etracker__details',
        'order_list' => 'mo/etracker/controllers/admin/mo_etracker__order_list',
        'order_article' => 'mo/etracker/controllers/admin/mo_etracker__order_article',
    ],
    'files' => [
        'mo_etracker__main' => 'mo/etracker/classes/mo_etracker__main.php',
        'mo_etracker__install' => 'mo/etracker/classes/mo_etracker__install.php',
        'mo_etracker__converter' => 'mo/etracker/classes/mo_etracker__converter.php',
        'mo_etracker__event' => 'mo/etracker/classes/mo_etracker__event.php',
        'mo_etracker__basketEvent' => 'mo/etracker/classes/events/mo_etracker__basketEvent.php',
        'mo_etracker__basketEmptiedEvent' => 'mo/etracker/classes/events/mo_etracker__basketEmptiedEvent.php',
        'mo_etracker__basketFilledEvent' => 'mo/etracker/classes/events/mo_etracker__basketFilledEvent.php',
        'mo_etracker__orderCanceledEvent' => 'mo/etracker/classes/events/mo_etracker__orderCanceledEvent.php',
        'mo_etracker__orderCompletedEvent' => 'mo/etracker/classes/events/mo_etracker__orderCompletedEvent.php',
        'mo_etracker__orderConfirmedEvent' => 'mo/etracker/classes/events/mo_etracker__orderConfirmedEvent.php',
        'mo_etracker__orderPartiallyCanceledEvent' => 'mo/etracker/classes/events/mo_etracker__orderPartiallyCanceledEvent.php',
        'mo_etracker__productViewedEvent' => 'mo/etracker/classes/events/mo_etracker__productViewedEvent.php',
        'mo_etracker__category' => 'mo/etracker/controllers/admin/mo_etracker__category.php',
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
        [
            'template' => 'headitem.tpl',
            'block' => 'admin_headitem_js',
            'file' => 'views/admin/blocks/admin_headitem_js.tpl'
        ],
    ],
    'settings' => [
        ['group' => 'mo_etracker__config', 'name' => 'mo_etracker__securecode', 'type' => 'str', 'value' => ''],
        ['group' => 'mo_etracker__config', 'name' => 'mo_etracker__securekey', 'type' => 'str', 'value' => ''],
        ['group' => 'mo_etracker__config', 'name' => 'mo_etracker__sechannel', 'type' => 'str', 'value' => ''],
        ['group' => 'mo_etracker__config', 'name' => 'mo_etracker__root', 'type' => 'str', 'value' => ''],
        ['group' => 'mo_etracker__config', 'name' => 'mo_etracker__debug', 'type' => 'bool', 'value' => false],
    ],
    'templates' => [
        'mo_etracker__eventhandler.tpl' => 'mo/etracker/views/tpl/eventhandler.tpl',
        'mo_etracker__etracker_include.tpl' => 'mo/etracker/views/tpl/etracker_include.tpl',
        'mo_etracker__category.tpl' => 'mo/etracker/views/admin/tpl/category.tpl',
    ],
    'events' => [
        'onActivate' => 'mo_etracker__install::onActivate',
        'onDeactivate' => 'mo_etracker__install::onDeactivate',
    ],
];

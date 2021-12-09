<?php

namespace Mediaopt\Etracker;

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id' => 'moEtracker',
    'title' => [
        'de' => 'mo: Etracker',
        'en' => 'mo: Etracker',
    ],
    'description' => [
        'de' => '<p>Erweitern Sie Ihren Shop um etracker Webcontrolling.</p><p><a href="https://projects.mediaopt.de/projects/mopt-twk/wiki/Dokumentation" target="_blank">Handbuch</a></p>',
        'en' => '<p>Add etracker features to your OXID shop.</p><p><a href="https://projects.mediaopt.de/projects/mopt-twk/wiki/Dokumentation" target="_blank">Handbook</a></p>'
    ],
    'lang' => 'en',
    'thumbnail' => 'logo.png',
    'version' => '2.1.10',
    'author' => 'Mediaopt GmbH',
    'url' => 'https://www.mediaopt.de',
    'email' => 'support@mediaopt.de',
    'extend' => [
        \OxidEsales\Eshop\Application\Model\UserBasket::class => Application\Model\UserBasket::class,
        \OxidEsales\Eshop\Application\Model\Order::class => Application\Model\Order::class,
        \OxidEsales\Eshop\Application\Model\Basket::class => Application\Model\Basket::class,
        \OxidEsales\Eshop\Core\ViewConfig::class => Core\ViewConfig::class,
        \OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class => Application\Controller\ArticleDetailsController::class,
        \OxidEsales\Eshop\Application\Controller\Admin\OrderList::class => Application\Controller\Admin\OrderList::class,
        \OxidEsales\Eshop\Application\Controller\Admin\OrderArticle::class => Application\Controller\Admin\OrderArticle::class
    ],
    'controllers' => [
        'mo_etracker__main' => \Mediaopt\Etracker\Main::class,
        'mo_etracker__install' => \Mediaopt\Etracker\Install::class,
        'mo_etracker__converter' => \Mediaopt\Etracker\Converter::class,
        'mo_etracker__event' => \Mediaopt\Etracker\Event::class,
        'mo_etracker__basketEvent' => \Mediaopt\Etracker\Event\BasketEvent::class,
        'mo_etracker__basketEmptiedEvent' => \Mediaopt\Etracker\Event\BasketEmptiedEvent::class,
        'mo_etracker__basketFilledEvent' => \Mediaopt\Etracker\Event\BasketFilledEvent::class,
        'mo_etracker__noticelistEmptiedEvent' => \Mediaopt\Etracker\Event\NoticeListEmptiedEvent::class,
        'mo_etracker__noticelistFilledEvent' => \Mediaopt\Etracker\Event\NoticeListFilledEvent::class,
        'mo_etracker__orderCanceledEvent' => \Mediaopt\Etracker\Event\OrderCanceledEvent::class,
        'mo_etracker__orderCompletedEvent' => \Mediaopt\Etracker\Event\OrderCompletedEvent::class,
        'mo_etracker__orderConfirmedEvent' => \Mediaopt\Etracker\Event\OrderConfirmedEvent::class,
        'mo_etracker__orderPartiallyCanceledEvent' => \Mediaopt\Etracker\Event\OrderPartiallyCanceledEvent::class,
        'mo_etracker__productViewedEvent' => \Mediaopt\Etracker\Event\ProductViewedEvent::class,
        'mo_etracker__category' => \Mediaopt\Etracker\Controller\Admin\CategoryController::class,
    ],
    'events' => [
        'onActivate' => '\Mediaopt\Etracker\Install::onActivate',
        'onDeactivate' => '\Mediaopt\Etracker\Install::onDeactivate',
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
        ]
    ],
    'settings' => [
        [
            'group' => 'mo_etracker__config',
            'name' => 'mo_etracker__securecode',
            'type' => 'str',
            'value' => '',
        ],
        [
            'group' => 'mo_etracker__config',
            'name' => 'mo_etracker__securekey',
            'type' => 'str',
            'value' => '',
        ],
        [
            'group' => 'mo_etracker__config',
            'name' => 'mo_etracker__sechannel',
            'type' => 'str',
            'value' => '',
        ],
        [
            'group' => 'mo_etracker__config',
            'name' => 'mo_etracker__root',
            'type' => 'str',
            'value' => '',
        ],
        [
            'group' => 'mo_etracker__config',
            'name' => 'mo_etracker__debug',
            'type' => 'bool',
            'value' => false,
        ],
    ],
    'templates' => [
        'mo_etracker__eventhandler.tpl' => 'mo/etracker/views/tpl/eventhandler.tpl',
        'mo_etracker__etracker_include.tpl' => 'mo/etracker/views/tpl/etracker_include.tpl',
        'mo_etracker__etracker_vars.tpl' => 'mo/etracker/views/tpl/etracker_vars.tpl',
        'mo_etracker__category.tpl' => 'mo/etracker/views/admin/tpl/category.tpl'
    ],
];

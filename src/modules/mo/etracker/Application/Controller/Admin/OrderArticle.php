<?php

namespace Mediaopt\Etracker\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This controller enables to cancel a single article.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend OrderArticle
 */
class OrderArticle extends OrderArticle_parent
{

    /**
     * @extend
     * @return string
     */
    public function render()
    {
        $this->addTplParam('mo_etracker__include', true);
        return parent::render();
    }

    /**
     * @extend
     */
    public function storno()
    {
        $orderArticle = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticle::class);
        $orderArticle->load(Registry::get(Request::class)->getRequestParameter('sArtID'));
        if ($orderArticle->oxorderarticles__oxstorno->value != 1) {
            $order = oxNew(Order::class);
            $order->load($orderArticle->oxorderarticles__oxorderid->value);
            $event = oxNew(\Mediaopt\Etracker\Event\OrderPartiallyCanceledEvent::class, $order, $orderArticle);
        }

        parent::storno();

        if (isset($event)) {
            Registry::get(\Mediaopt\Etracker\Main::class)->trigger($event);
        }
    }
}

<?php

namespace Mediaopt\Etracker\Application\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Registry;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
 */

/**
 * This class issues an event if a new order has been placed and confirms it immediately.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend oxOrder
 */
class Order extends Order_parent
{

    /**
     * @extend
     */
    public function cancelOrder()
    {
        parent::cancelOrder();
        Registry::get(\Mediaopt\Etracker\Main::class)->trigger(oxNew(\Mediaopt\Etracker\Event\OrderCanceledEvent::class, $this));
    }

    /**
     * @extend
     * @param Basket $basket
     * @param $user
     * @param bool $recalculatingOrder
     * @return int
     */
    public function finalizeOrder(Basket $basket, $user, bool $recalculatingOrder = false): int
    {
        $isNewOrder = empty($this->oxorder__oxordernr->value);
        $status = parent::finalizeOrder($basket, $user, $recalculatingOrder);
        if ($status === oxNew(\OxidEsales\Eshop\Application\Model\Order::class)::ORDER_STATE_OK && $isNewOrder) {
            Registry::get(\Mediaopt\Etracker\Main::class)->trigger(oxNew(\Mediaopt\Etracker\Event\OrderCompletedEvent::class, $this, $basket))->trigger(oxNew(\Mediaopt\Etracker\Event\OrderConfirmedEvent::class, $this));
        }
        return $status;
    }

}

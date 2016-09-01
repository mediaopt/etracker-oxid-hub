<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This class issues an event if a new order has been placed and confirms it immediately.
 *
 * @author Andre Moelle <andre.moelle@mediaopt.de>
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend oxOrder
 */
class mo_etracker__oxorder extends mo_etracker__oxorder_parent
{

    /**
     * @extend
     */
    public function cancelOrder()
    {
        parent::cancelOrder();
        \oxRegistry::get('mo_etracker__main')->trigger(new mo_etracker__orderCanceledEvent($this));
    }

    /**
     * @extend
     * @param oxBasket $basket
     * @param $user
     * @param bool $recalculatingOrder
     */
    public function finalizeOrder(\oxBasket $basket, $user, $recalculatingOrder = false)
    {
        $isNewOrder = empty($this->oxorder__oxordernr->value);
        $status = parent::finalizeOrder($basket, $user, $recalculatingOrder);
        if ($status === oxOrder::ORDER_STATE_OK && $isNewOrder) {
            \oxRegistry::get('mo_etracker__main')
                ->trigger(new mo_etracker__orderCompletedEvent($this, $basket))
                ->trigger(new mo_etracker__orderConfirmedEvent($this));
        }
    }

}
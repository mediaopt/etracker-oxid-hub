<?php

/**
 * TODO
 *
 * @author derksen mediaopt GmbH
 * @extend oxorder
 */
class mo_etracker__oxorder extends mo_etracker__oxorder_parent
{

    /**
     * @extend
     */
    public function cancelOrder()
    {
        parent::cancelOrder();
        \oxRegistry::get('mo_etracker__helper')->trigger(new mo_etracker__orderCanceledEvent($this));
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
            \oxRegistry::get('mo_etracker__helper')
                ->trigger(new mo_etracker__orderCompletedEvent($this, $basket))
                ->trigger(new mo_etracker__orderConfirmedEvent($this));
        }
    }

}
<?php

/**
 *
 * @author derksen mediaopt GmbH
 */
class mo_etracker__orderCanceledEvent implements mo_etracker__event
{

    /**
     * @var string
     */
    protected $orderNumber = '';

    /**
     * @see mo_etracker__converter::fromBasketItem
     * @var array
     */
    protected $cancelledProducts = [];

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'orderPartialCancellation';
    }

    public function getParameters()
    {
        return [$this->orderNumber, $this->cancelledProducts];
    }
}
<?php

/**
 *
 * @author derksen mediaopt GmbH
 */
class mo_etracker__orderConfirmedEvent implements mo_etracker__event
{

    /**
     * @var string
     */
    protected $orderNumber = '';

    /**
     * @param oxOrder $order
     */
    public function __construct(\oxOrder $order)
    {
        $this->orderNumber = (string)$order->oxorder__oxordernr->value;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'orderConfirmation';
    }

    public function getParameters()
    {
        return [$this->orderNumber];
    }


}
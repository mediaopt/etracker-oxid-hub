<?php

/**
 *
 * @author derksen mediaopt GmbH
 */
class mo_etracker__orderPartiallyCanceledEvent implements mo_etracker__event
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

    public function __construct(\oxOrder $order, \oxOrderArticle $orderArticle)
    {
        $this->orderNumber = $order->oxorder__oxordernr->value;
        $this->cancelledProducts = [\oxRegistry::get('mo_etracker__converter')->fromOrderArticle($orderArticle)];
    }

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
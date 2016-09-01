<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */


/**
 * This event is issued if an article in an order has been canceled.
 *
 * @author Andre Moelle <andre.moelle@mediaopt.de>
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
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

    /**
     * @param oxOrder $order
     * @param oxOrderArticle $orderArticle
     */
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

    /**
     * @see mo_etracker__event::getParameters()
     * @return array
     */
    public function getParameters()
    {
        return [$this->orderNumber, $this->cancelledProducts];
    }
}
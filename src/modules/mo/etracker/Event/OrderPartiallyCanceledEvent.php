<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */


/**
 * This event is issued if an article in an order has been canceled.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class orderPartiallyCanceledEvent implements event
{

    /**
     * @var string
     */
    protected $orderNumber = '';

    /**
     * @see converter::fromBasketItem
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
        $this->cancelledProducts = [\oxRegistry::get('converter')->fromOrderArticle($orderArticle)];
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'orderPartialCancellation';
    }

    /**
     * @return array
     *@see event::getParameters()
     */
    public function getParameters()
    {
        return [$this->orderNumber, $this->cancelledProducts];
    }
}
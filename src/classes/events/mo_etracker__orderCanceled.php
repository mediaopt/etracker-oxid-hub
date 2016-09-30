<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */


/**
 * This event is issued if an order has been canceled.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class mo_etracker__orderCanceledEvent implements mo_etracker__event
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
        $this->orderNumber = $order->oxorder__oxordernr->value;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'orderCancellation';
    }

    /**
     * @see mo_etracker__event::getParameters()
     * @return array
     */
    public function getParameters()
    {
        return [$this->orderNumber];
    }

}
<?php

namespace Mediaopt\Etracker\Event;

use OxidEsales\Eshop\Application\Model\Order;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
 */


/**
 * This event is issued if an order has been canceled.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class OrderCanceledEvent implements \Mediaopt\Etracker\Event
{

    /**
     * @var string
     */
    protected $orderNumber = '';

    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->orderNumber = $order->oxorder__oxordernr->value;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return 'orderCancellation';
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return [$this->orderNumber];
    }

}

<?php

namespace Mediaopt\Etracker\Event;

use OxidEsales\Eshop\Application\Model\Order;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
 */


/**
 * This event is issued if an order has been confirmed.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class OrderConfirmedEvent implements \Mediaopt\Etracker\Event
{

    /**
     * @var string
     */
    protected string $orderNumber = '';

    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->orderNumber = (string)$order->oxorder__oxordernr->value;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return 'orderConfirmation';
    }

    /**
     * @return array
     * @see mo_etracker__event::getParameters()
     */
    public function getParameters(): array
    {
        return [$this->orderNumber];
    }
}

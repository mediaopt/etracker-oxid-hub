<?php

namespace Mediaopt\Etracker\Event;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
 */


/**
 * This event is issued if an order has been completed.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class OrderCompletedEvent implements \Mediaopt\Etracker\Event
{

    /**
     * @see mo_etracker__converter::fromOrder()
     */
    protected $order;

    /**
     * Optional.
     *
     * @var string
     */
    protected $pageName = '';

    /**
     * @param Order $order
     * @param Basket $basket
     * @param string $pageName
     */
    public function __construct(Order $order, Basket $basket, string $pageName = '')
    {
        $this->order = Registry::get(\Mediaopt\Etracker\Converter::class)->fromOrder($order, $basket);
        $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return 'order';
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        $parameters = [$this->order];
        if (!empty($this->pageName)) {
            $parameters[] = $this->pageName;
        }
        return $parameters;
    }
}

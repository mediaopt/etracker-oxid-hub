<?php

namespace Mediaopt\Etracker\Event;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\OrderArticle;
use OxidEsales\Eshop\Core\Registry;

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
class OrderPartiallyCanceledEvent implements \Mediaopt\Etracker\Event
{

    /**
     * @var string
     */
    protected $orderNumber = '';

    /**
     * @var array
     */
    protected array $cancelledProducts = [];

    /**
     * @param Order $order
     * @param OrderArticle $orderArticle
     */
    public function __construct(Order $order, OrderArticle $orderArticle)
    {
        $this->orderNumber = $order->oxorder__oxordernr->value;
        $this->cancelledProducts = [Registry::get(\Mediaopt\Etracker\Converter::class)->fromOrderArticle($orderArticle)];
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return 'orderPartialCancellation';
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return [$this->orderNumber, $this->cancelledProducts];
    }
}

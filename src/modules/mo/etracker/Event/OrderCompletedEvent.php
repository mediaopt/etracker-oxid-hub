<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */


/**
 * This event is issued if an order has been completed.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class orderCompletedEvent implements event
{

    /**
     * @see converter::fromOrder()
     * @var stdClass
     */
    protected $order;

    /**
     * Optional.
     *
     * @var string
     */
    protected $pageName = '';

    /**
     * @param \oxOrder $order
     * @param \oxBasket $basket
     * @param string $pageName
     */
    public function __construct(\oxOrder $order, \oxBasket $basket, $pageName = '')
    {
        $this->order = \oxRegistry::get('converter')->fromOrder($order, $basket);
        $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'order';
    }

    /**
     * @return array
     *@see event::getParameters()
     */
    public function getParameters()
    {
        $parameters = [$this->order];
        if (!empty($this->pageName)) {
            $parameters[] = $this->pageName;
        }
        return $parameters;
    }
    
}
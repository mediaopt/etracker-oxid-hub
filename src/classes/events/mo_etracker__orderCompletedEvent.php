<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */


/**
 * This event is issued if an order has been completed.
 *
 * @author Andre Moelle <andre.moelle@mediaopt.de>
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class mo_etracker__orderCompletedEvent implements mo_etracker__event
{

    /**
     * @see mo_etracker__converter::fromOrder()
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
        $this->order = \oxRegistry::get('mo_etracker__converter')->fromOrder($order, $basket);
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
     * @see mo_etracker__event::getParameters()
     * @return array
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
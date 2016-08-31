<?php

/**
 *
 * @author derksen mediaopt GmbH
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
     * @param oxOrder $order
     */
    public function __construct(\oxOrder $order, \oxBasket $basket)
    {
        $this->order = \oxRegistry::get('mo_etracker__converter')->fromOrder($order, $basket);
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
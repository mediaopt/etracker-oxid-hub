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
     * @var string
     */
    protected $pageName = '';

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
<?php

/**
 *
 * @author derksen mediaopt GmbH
 */
class mo_etracker__orderCanceledEvent implements mo_etracker__event
{

    /**
     * @var string
     */
    protected $orderNumber = '';

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'orderCancellation';
    }

    public function getParameters()
    {
        return [$this->orderNumber];
    }

}
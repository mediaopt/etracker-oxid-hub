<?php

/**
 *
 * @author derksen mediaopt GmbH
 */
class mo_etracker__orderConfirmedEvent implements mo_etracker__event
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
        return 'orderConfirmation';
    }

    public function getParameters()
    {
        return [$this->orderNumber];
    }


}
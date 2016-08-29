<?php

/**
 *
 * @author derksen mediaopt GmbH
 */
class mo_etracker__basketFilledEvent extends mo_etracker__basketEvent implements mo_etracker__event
{

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'insertToBasket';
    }

}
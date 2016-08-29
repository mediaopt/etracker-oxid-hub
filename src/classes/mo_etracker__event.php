<?php

/**
 *
 * @author derksen mediaopt GmbH
 */
interface mo_etracker__event
{

    /**
     * @return string
     */
    public function getEventName();

    /**
     * @return array
     */
    public function getParameters();

}
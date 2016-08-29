<?php

class mo_etracker__details extends mo_etracker__details_parent
{

    /**
     * @extend
     * @return string
     */
    public function render()
    {
        // TODO: How to find out a basket id? (session id?!)
        // TODO: When to set a page name?
        \oxRegistry::get('mo_etracker__helper')->trigger(new mo_etracker__productViewedEvent($this->getProduct()));
        return parent::render();
    }
}
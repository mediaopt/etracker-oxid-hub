<?php

class mo_etracker__details extends mo_etracker__details_parent
{

    /**
     * @extend
     * @return string
     */
    public function render()
    {
        $event = new mo_etracker__productViewedEvent(
            $this->getProduct(),
            \oxRegistry::getSession()->getBasket()->mo_etracker__getBasketId()
        );
        \oxRegistry::get('mo_etracker__helper')->trigger($event);
        return parent::render();
    }
}
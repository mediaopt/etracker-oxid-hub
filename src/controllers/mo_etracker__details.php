<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This class issues a event that a product has been viewed.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend Details
 */
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
        \oxRegistry::get('mo_etracker__main')->trigger($event);
        return parent::render();
    }
}
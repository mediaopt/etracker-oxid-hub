<?php

namespace Mediaopt\Etracker\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

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
class ArticleDetailsController extends ArticleDetailsController_parent
{

    /**
     * @extend
     * @return string
     */
    public function render()
    {
        $event = oxNew(\Mediaopt\Etracker\Event\ProductViewedEvent::class, $this->getProduct(), Registry::getSession()->getBasket()->mo_etracker__getBasketId());
        Registry::get(\Mediaopt\Etracker\Main::class)->trigger($event);
        return parent::render();
    }
}

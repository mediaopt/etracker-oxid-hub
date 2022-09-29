<?php

namespace Mediaopt\Etracker\Application\Controller;

use Mediaopt\Etracker\Event\ProductViewedEvent;
use OxidEsales\Eshop\Core\Registry;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
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
    public function render(): string
    {
        $event = oxNew(ProductViewedEvent::class, $this->getProduct(), Registry::getSession()->getBasket()->mo_etracker__getBasketId());
        Registry::get(\Mediaopt\Etracker\Main::class)->trigger($event);
        return parent::render();
    }
}

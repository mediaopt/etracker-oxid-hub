<?php

namespace Mediaopt\Etracker\Application\Model;

use Mediaopt\Etracker\Event\BasketEmptiedEvent;
use Mediaopt\Etracker\Event\BasketFilledEvent;
use Mediaopt\Etracker\Main;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Core\Registry;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
 */

/**
 * This class issues an event whenever an item has been added to or removed from the basket.
 *
 * Additionally, it sets a unique id, which will be transmitted to etracker.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend oxBasket
 */
class Basket extends Basket_parent
{

    /**
     * @var string
     */
    protected $mo_etracker__basketId = '';

    /**
     *
     *
     * @extend
     * @param string $productId
     * @param float $amount
     * @param null $selection
     * @param null $persistentParameters
     * @param bool $override
     * @param bool $bundle
     * @param null $oldBasketItemId
     * @return BasketItem
     */
    public function addToBasket($productId, $amount, $selection = null, $persistentParameters = null, $override = false, $bundle = false, $oldBasketItemId = null)
    {
        $itemId = empty($oldBasketItemId) ? $this->getItemKey($productId, $selection, $persistentParameters, $bundle) : $oldBasketItemId;
        $previousBasketItem = $this->_aBasketContents[$itemId] ?? null;
        $previousAmount = is_null($previousBasketItem) ? 0 : $previousBasketItem->getAmount();

        /** @var BasketItem $basketItem */
        $basketItem = parent::addToBasket($productId, $amount, $selection, $persistentParameters, $override, $bundle, $oldBasketItemId);
        assert(!is_null($basketItem) || !is_null($previousBasketItem));

        $pertainedBasketItem = is_null($basketItem) ? $previousBasketItem : $basketItem;
        $currentAmount = is_null($basketItem) ? 0 : $basketItem->getAmount();
        $event = $this->mo_etracker__generateEvent($pertainedBasketItem, $currentAmount - $previousAmount);
        if (!is_null($event)) {
            Registry::get(Main::class)->trigger($event);
        }
        return $basketItem;
    }

    /**
     * @param BasketItem $basketItem
     * @param int $amountDelta
     * @return BasketFilledEvent|BasketEmptiedEvent
     */
    protected function mo_etracker__generateEvent($basketItem, $amountDelta)
    {
        if ($amountDelta > 0) {
            return oxNew(BasketFilledEvent::class, $basketItem->getArticle(), $amountDelta);
        }
        if ($amountDelta < 0) {
            return oxNew(BasketEmptiedEvent::class, $basketItem->getArticle(), -$amountDelta);
        }

        return null;
    }

    /**
     * If this basket has not a basket id, we generate one, set it and return it.
     *
     * @return string
     */
    public function mo_etracker__getBasketId()
    {
        if (empty($this->mo_etracker__basketId)) {
            $this->mo_etracker__basketId = md5(uniqid());
        }
        return $this->mo_etracker__basketId;
    }
}

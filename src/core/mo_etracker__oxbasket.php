<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This class issues an event whenever an item has been added to or removed from the basket.
 *
 * Additionally, it sets a unique id, which will be transmitted to etracker.
 *
 * @author Andre Moelle <andre.moelle@mediaopt.de>
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend oxBasket
 */
class mo_etracker__oxbasket extends mo_etracker__oxbasket_parent
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
     * @return oxBasketItem
     */
    public function addToBasket(
        $productId,
        $amount,
        $selection = null,
        $persistentParameters = null,
        $override = false,
        $bundle = false,
        $oldBasketItemId = null
    ) {
        $itemId = empty($oldBasketItemId)
            ? $this->getItemKey($productId, $selection, $persistentParameters, $bundle)
            : $oldBasketItemId;
        $previousBasketItem = isset($this->_aBasketContents[$itemId]) ? $this->_aBasketContents[$itemId] : null;
        $previousAmount = is_null($previousBasketItem) ? 0 : $previousBasketItem->getAmount();

        /** @var oxBasketItem $basketItem */
        $basketItem = parent::addToBasket($productId, $amount, $selection, $persistentParameters, $override, $bundle,
            $oldBasketItemId);

        assert(!is_null($basketItem) || !is_null($previousBasketItem));

        $pertainedBasketItem = is_null($basketItem) ? $previousBasketItem : $basketItem;
        $currentAmount = is_null($basketItem) ? 0 : $basketItem->getAmount();
        $event = $this->mo_etracker__generateEvent($pertainedBasketItem, $currentAmount - $previousAmount);
        if (!is_null($event)) {
            \oxRegistry::get('mo_etracker__main')->trigger($event);
        }
        return $basketItem;
    }

    /**
     * @param \oxBasketItem $basketItem
     * @param int $amountDelta
     * @return mo_etracker__basketFilledEvent
     */
    protected function mo_etracker__generateEvent($basketItem, $amountDelta)
    {
        // TODO: When to set a page name?
        $basketId = $this->mo_etracker__getBasketId();
        if ($amountDelta > 0) {
            return new mo_etracker__basketFilledEvent($basketItem->getArticle(), $amountDelta, $basketId);
        }
        if ($amountDelta < 0) {
            return new mo_etracker__basketEmptiedEvent($basketItem->getArticle(), -$amountDelta, $basketId);
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
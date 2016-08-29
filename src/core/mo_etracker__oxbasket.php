<?php

class mo_etracker__oxbasket extends mo_etracker__oxbasket_parent
{

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
        $previousAmount = isset($this->_aBasketContents[$itemId]) ? $this->_aBasketContents[$itemId]->getAmount() : 0;

        /** @var oxBasketItem $basketItem */
        $basketItem = parent::addToBasket($productId, $amount, $selection, $persistentParameters, $override, $bundle,
            $oldBasketItemId);

        $event = $this->generateEvent($basketItem, $basketItem->getAmount() - $previousAmount);
        if(!is_null($event)) {
            \oxRegistry::get('mo_etracker__helper')->trigger($event);
        }
        return $basketItem;
    }

    public function removeItem($itemKey)
    {
        parent::removeItem($itemKey);
    }

    /**
     * @param \oxBasketItem $basketItem
     * @param int $amountDelta
     * @return mo_etracker__basketFilledEvent
     */
    protected function generateEvent($basketItem, $amountDelta)
    {
        // TODO: How to find out a basket id? (session id?!)
        // TODO: When to set a page name?

        if($amountDelta > 0) {
            return new mo_etracker__basketFilledEvent($basketItem->getArticle(), $amountDelta);
        }
        if($amountDelta < 0) {
            return new mo_etracker__basketEmptiedEvent($basketItem->getArticle(), -$amountDelta);
        }

        return null;
    }
}
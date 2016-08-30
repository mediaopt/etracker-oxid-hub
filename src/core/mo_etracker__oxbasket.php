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
        $previousBasketItem = isset($this->_aBasketContents[$itemId]) ? $this->_aBasketContents[$itemId] : null;
        $previousAmount = is_null($previousBasketItem) ? 0 : $previousBasketItem->getAmount();

        /** @var oxBasketItem $basketItem */
        $basketItem = parent::addToBasket($productId, $amount, $selection, $persistentParameters, $override, $bundle,
            $oldBasketItemId);

        assert(!is_null($basketItem) || !is_null($previousBasketItem));

        $pertainedBasketItem = is_null($basketItem) ? $previousBasketItem : $basketItem;
        $currentAmount = is_null($basketItem) ? 0 : $basketItem->getAmount();
        $event = $this->generateEvent($pertainedBasketItem, $currentAmount - $previousAmount);
        if(!is_null($event)) {
            \oxRegistry::get('mo_etracker__helper')->trigger($event);
        }
        return $basketItem;
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
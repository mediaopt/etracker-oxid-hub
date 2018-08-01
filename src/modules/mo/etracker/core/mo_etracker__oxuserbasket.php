<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This class issues an event whenever an item has been added to or removed from the noticelist.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend oxUserBasket
 */
class mo_etracker__oxuserbasket extends mo_etracker__oxuserbasket_parent
{

    /**
     *
     *
     * @extend
     *
     * @param string $productId        Article ID
     * @param double $amount           Product amount
     * @param array  $selectionList    product select lists
     * @param bool   $isOverride       if true overrides $dAmount, else sums previous with current it
     * @param array  $persistentParams product persistent parameters (default null)
     *
     * @return integer
     */
    public function addItemToBasket($productId = null, $amount = null, $selectionList = null, $isOverride = false, $persistentParams = null)
    {
        if ($this->oxuserbaskets__oxtitle->value !== 'noticelist') {
            return parent::addItemToBasket($productId, $amount, $selectionList, $isOverride, $persistentParams);
        }

        $previousBasketItem = $this->getItem($productId, $selectionList, $persistentParams);
        $previousAmount = $previousBasketItem->oxuserbasketitems__oxamount->value;

        $addedAmount = parent::addItemToBasket($productId, $amount, $selectionList, $isOverride, $persistentParams);
        $basketItem = $this->getItem($productId, $selectionList, $persistentParams);

        assert(!is_null($basketItem) || !is_null($previousBasketItem));

        $currentAmount = $basketItem->oxuserbasketitems__oxamount->value;

        $event = $this->mo_etracker__generateEvent($basketItem->getArticle($this->_getItemKey($productId, $selectionList, $persistentParams)), ($amount === 0.0 || $amount === 0) ? -$previousAmount : $currentAmount - $previousAmount);
        if (!is_null($event)) {
            \oxRegistry::get('mo_etracker__main')->trigger($event);
        }

        return $addedAmount;
    }

    /**
     * @param \oxArticle    $article
     * @param int           $amountDelta
     *
     * @return mo_etracker__noticelistFilledEvent|mo_etracker__noticelistEmptiedEvent|null
     */
    protected function mo_etracker__generateEvent($article, $amountDelta)
    {
        if ($amountDelta > 0) {
            return \oxNew('mo_etracker__noticelistFilledEvent', $article, $amountDelta);
        }
        if ($amountDelta < 0) {
            return \oxNew('mo_etracker__noticelistEmptiedEvent', $article, -$amountDelta);
        }

        return null;
    }
}
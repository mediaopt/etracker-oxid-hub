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
     * @param string $sProductId Article ID
     * @param double $dAmount    Product amount
     * @param array  $aSel       product select lists
     * @param bool   $blOverride if true overrides $dAmount, else sums previous with current it
     * @param array  $aPersParam product persistent parameters (default null)
     *
     * @return integer
     */
    public function addItemToBasket($sProductId = null, $dAmount = null, $aSel = null, $blOverride = false, $aPersParam = null) {

        if ($this->oxuserbaskets__oxtitle->value !== 'noticelist') {
            return parent::addItemToBasket($sProductId, $dAmount, $aSel, $blOverride, $aPersParam);
        }

        $previousBasketItem = $this->getItem($sProductId, $aSel, $aPersParam);
        $previousAmount = $previousBasketItem->oxuserbasketitems__oxamount->value;

        $amount = parent::addItemToBasket($sProductId, $dAmount, $aSel, $blOverride, $aPersParam);
        $basketItem = $this->getItem($sProductId, $aSel, $aPersParam);

        assert(!is_null($basketItem) || !is_null($previousBasketItem));

        $currentAmount = $basketItem->oxuserbasketitems__oxamount->value;

        $event = $this->mo_etracker__generateEvent($basketItem->getArticle($this->_getItemKey($sProductId, $aSel, $aPersParam)), $dAmount === 0.0 ? -$previousAmount : $currentAmount - $previousAmount);
        if (!is_null($event)) {
            \oxRegistry::get('mo_etracker__main')->trigger($event);
        }
        return $amount;
    }

    /**
     * @param \oxArticle    $article
     * @param int           $amountDelta
     *
     * @return mo_etracker__noticelistFilledEvent|mo_etracker__noticelistEmptiedEvent
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
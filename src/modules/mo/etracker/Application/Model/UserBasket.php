<?php

namespace Mediaopt\Etracker\Application\Model;

use Mediaopt\Etracker\Event\NoticelistEmptiedEvent;
use Mediaopt\Etracker\Event\NoticelistFilledEvent;
use Mediaopt\Etracker\Main;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
 */

/**
 * This class issues an event whenever an item has been added to or removed from the noticelist.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend oxUserBasket
 */
class UserBasket extends UserBasket_parent
{

    /**
     * @extend
     *
     * @return
     */
    public function addItemToBasket($productId = null, $amount = null, $selectionList = null, $isOverride, $persistentParams = null)
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

        $event = $this->mo_etracker__generateEvent($basketItem->getArticle($this->_getItemKey($productId, $selectionList, $persistentParams)), $amount === 0.0 || $amount === 0 ? -$previousAmount : $currentAmount - $previousAmount);
        if (!is_null($event)) {
            Registry::get(Main::class)->trigger($event);
        }

        return $addedAmount;
    }

    /**
     * @param Article $article
     * @param int $amountDelta
     *
     * @return NoticelistFilledEvent|NoticelistEmptiedEvent|null
     */
    protected function mo_etracker__generateEvent(Article $article, int $amountDelta)
    {
        if ($amountDelta > 0) {
            return oxNew(NoticelistFilledEvent::class, $article, $amountDelta);
        }
        if ($amountDelta < 0) {
            return oxNew(NoticelistEmptiedEvent::class, $article, -$amountDelta);
        }

        return null;
    }
}

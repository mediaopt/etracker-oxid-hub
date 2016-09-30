<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This class provides functionality to convert OXID artifacts into the corresponding etracker artifacts.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 */
class mo_etracker__converter
{

    /**
     * Since the etracker API does not accept empty properties, we have to remove them.
     *
     * @param object $object
     */
    private function removeEmptyProperties($object)
    {
        foreach ($object as $property => $value) {
            if (empty($value)) {
                unset($object->$property);
            }
        }
    }

    /**
     * Returns a list of category labels associated with the given article.
     *
     * If any of the categories has a non-empty etracker category name, only etracker category names will be used.
     * Otherwise, the category names are used.
     *
     * If more than four categories are available, only the last four will be returned.
     *
     * @param oxArticle $article
     * @return string[]
     */
    protected function getCategories(\oxArticle $article)
    {
        $categories = [];
        $etrackerCategories = [];
        $parent = $article->getCategory();
        while (!is_null($parent)) {
            array_unshift($categories, $parent->getTitle());
            array_unshift($etrackerCategories, $parent->oxcategories__mo_etracker__name->value);
            $parent = $parent->getParentCategory();
        }
        $etrackerCategories = array_filter($etrackerCategories);
        return array_slice(empty($etrackerCategories) ? $categories : $etrackerCategories, -4);
    }

    /**
     * Returns variant information about an article.
     *
     * @param oxArticle $product
     * @return stdClass
     */
    protected function getVariant(\oxArticle $product)
    {
        $variants = new stdClass();
        if (!$product->isVariant()) {
            return $variants;
        }

        // The | is magic, but this should not pose any problem.
        $variantNames = explode('|', $product->oxarticles__oxvarname);
        $selectedVariants = explode('|', $product->oxarticles__oxvarselect);

        // We can transmit up to three variants. For now, we choose the first three.
        for ($i = 0; $i < min(3, count($variantNames), count($selectedVariants)); $i++) {
            $variants->{trim($variantNames[$i])} = trim($selectedVariants[$i]);
        }
        return $variants;
    }

    /**
     * @param oxArticle $article
     * @param stdClass $etrackerProduct
     */
    protected function enrichProductWithManufacturer(\oxArticle $article, stdClass $etrackerProduct)
    {
        /** @var oxManufacturer $manufacturer */
        $manufacturer = $article->getManufacturer();
        if (!is_null($manufacturer)) {
            $etrackerProduct->pro_name = $manufacturer->getTitle();
        }
    }

    /**
     * @param oxArticle $article
     * @param stdClass $etrackerProduct
     */
    protected function enrichProductWithEAN(\oxArticle $article, stdClass $etrackerProduct)
    {
        if (!empty($article->oxarticles__oxean->value)) {
            $etrackerProduct->ean = $article->oxarticles__oxean->value;
        }
    }

    /**
     * @param \oxArticle $article
     * @param stdClass $etrackerProduct
     */
    protected function enrichProduct(\oxArticle $article, stdClass $etrackerProduct)
    {
        $this->enrichProductWithManufacturer($article, $etrackerProduct);
        $this->enrichProductWithEAN($article, $etrackerProduct);
    }

    /**
     * Determines the product id to be used for a product.
     *
     * @param \oxArticle|\oxOrderArticle $article
     * @return string
     */
    protected function determineProductId($article)
    {
        if (!empty($article->oxarticles__oxartnum->value)) {
            return $article->oxarticles__oxartnum->value;
        }
        return $article->getProductId();
    }

    /**
     * Returns a product containing the information about the supplied article.
     *
     * @param \oxArticle|\oxOrderArticle $article
     * @return stdClass
     */
    public function fromArticle($article)
    {
        if ($article instanceof oxOrderArticle) {
            $actualArticle = \oxNew('oxArticle');
            $actualArticle->load($article->oxorderarticles__oxartid->value);
            return $this->fromArticle($actualArticle);
        }

        $etrackerProduct = new stdClass();
        $etrackerProduct->id = $this->determineProductId($article);
        $etrackerProduct->name = $article->oxarticles__oxtitle->value;
        $etrackerProduct->category = $this->getCategories($article);
        $etrackerProduct->price = (string)$article->getPrice()->getPrice(); // TODO: gross/net price?
        $etrackerProduct->currency = \oxRegistry::getConfig()->getActShopCurrencyObject()->name;
        $etrackerProduct->variants = $this->getVariant($article);
        $this->enrichProduct($article, $etrackerProduct);
        return $etrackerProduct;
    }

    /**
     * Returns a basket item containing the information of the supploed basket item.
     *
     * @param \oxBasketItem $basketItem
     * @return stdClass
     */
    public function fromBasketItem(\oxBasketItem $basketItem)
    {
        $etrackerBasketItem = new stdClass();
        $etrackerBasketItem->product = $this->fromArticle($basketItem->getArticle());
        $etrackerBasketItem->quantity = $basketItem->getAmount();
        return $etrackerBasketItem;
    }

    /**
     * Returns a basket item containing the information of the supplied order article.
     *
     * @param oxOrderArticle $orderArticle
     * @return stdClass
     */
    public function fromOrderArticle(\oxOrderArticle $orderArticle)
    {
        $etrackerBasketItem = new stdClass();
        $etrackerBasketItem->product = $this->fromArticle($orderArticle);
        $etrackerBasketItem->quantity = (int)$orderArticle->oxorderarticles__oxamount->value;
        return $etrackerBasketItem;
    }

    /**
     * Returns a basket item containing the information about the supplied wrapping.
     *
     * @param oxWrapping $wrapping
     * @return stdClass
     */
    protected function fromWrapping(\oxWrapping $wrapping)
    {
        $etrackerBasketItem = new stdClass();
        $etrackerBasketItem->product = new stdClass();
        $etrackerBasketItem->product->id = $wrapping->getId();
        $etrackerBasketItem->product->name = $wrapping->oxwrapping__oxname->value;
        $etrackerBasketItem->product->category = strcasecmp($wrapping->oxwrapping__oxtype->value, 'WRAP') === 0
            ? ['Wrapping'] : ['Gift Card'];
        $etrackerBasketItem->product->price = (string)$wrapping->getPrice();
        $etrackerBasketItem->product->currency = \oxRegistry::getConfig()->getActShopCurrencyObject()->name;
        $etrackerBasketItem->product->variants = new stdClass();
        $etrackerBasketItem->quantity = 1;
        return $etrackerBasketItem;
    }

    /**
     * Returns the vouchers (as basket item) used in the supplied basket.
     *
     * @param mo_etracker__oxbasket $basket
     * @return stdClass[]
     */
    protected function getVouchers(\mo_etracker__oxbasket $basket)
    {
        $products = [];
        foreach ($basket->getVouchers() as $voucherObject) {
            $voucher = \oxNew('oxVoucher');
            $voucher->load($voucherObject->sVoucherId);
            $etrackerBasketItem = new stdClass();
            $etrackerBasketItem->product = new stdClass();
            $etrackerBasketItem->product->id = $voucherObject->sVoucherNr;
            $etrackerBasketItem->product->name = $voucher->getSerie()->oxvoucherseries__oxserienr->value;
            $etrackerBasketItem->product->category = ['Voucher'];
            $etrackerBasketItem->product->price = '-' . $voucherObject->dVoucherdiscount;
            $etrackerBasketItem->product->currency = \oxRegistry::getConfig()->getActShopCurrencyObject()->name;
            $etrackerBasketItem->product->variants = new stdClass();
            $etrackerBasketItem->quantity = 1;
            $products[] = $etrackerBasketItem;
        }
        return $products;
    }

    /**
     * Returns the discounts (as basket item) of the supplied basket.
     *
     * @param mo_etracker__oxbasket $basket
     * @return stdClass[]
     */
    protected function getDiscountsFromBasket(\mo_etracker__oxbasket $basket)
    {
        $products = [];
        foreach ($basket->getDiscounts() as $discount) {
            $etrackerBasketItem = new stdClass();
            $etrackerBasketItem->product = new stdClass();
            $etrackerBasketItem->product->id = $discount->sOXID;
            $etrackerBasketItem->product->name = $discount->sDiscount;
            $etrackerBasketItem->product->category = ['Discount', 'Basket'];
            $etrackerBasketItem->product->price = '-' . $discount->dDiscount;
            $etrackerBasketItem->product->currency = \oxRegistry::getConfig()->getActShopCurrencyObject()->name;
            $etrackerBasketItem->product->variants = new stdClass();
            $etrackerBasketItem->quantity = 1;
            $products[] = $etrackerBasketItem;
        }
        return $products;
    }

    /**
     * Returns the discounts (as basket item) of the supplied basket item.
     *
     * @param mo_etracker__oxbasket $basket
     * @param oxBasketItem $basketItem
     * @return stdClass[]
     */
    protected function getDiscountsFromBasketItem(\mo_etracker__oxbasket $basket, \oxBasketItem $basketItem)
    {
        $initialPrice = $basketItem->getArticle()->getPrice();
        $previousPrice = $initialPrice->getPrice();
        $products = [];
        foreach (\oxNew('oxDiscountList')->getBasketItemDiscounts($basketItem->getArticle(), $basket) as $discount) {
            /** @var \oxDiscount $discount */
            $initialPrice->setDiscount($discount->getAddSum(), $discount->getAddSumType());
            $currentPrice = clone $initialPrice;
            $currentPrice->calculateDiscount();
            $discountedValue = $previousPrice - $currentPrice->getPrice();
            $previousPrice = $currentPrice->getPrice();

            $etrackerBasketItem = new stdClass();
            $etrackerBasketItem->product = new stdClass();
            $etrackerBasketItem->product->id = $discount->getId();
            $etrackerBasketItem->product->name = $discount->oxdiscount__oxtitle->value;
            $etrackerBasketItem->product->category = ['Discount', 'Article'];
            $etrackerBasketItem->product->price = '-' . $discountedValue;
            $etrackerBasketItem->product->currency = \oxRegistry::getConfig()->getActShopCurrencyObject()->name;
            $etrackerBasketItem->product->variants = new stdClass();
            $etrackerBasketItem->quantity = 1;
            $products[] = $etrackerBasketItem;
        }
        return $products;
    }

    /**
     * Returns the items of a basket.
     *
     * @param mo_etracker__oxbasket $basket
     * @return stdClass[]
     */
    protected function getBasketItems(\mo_etracker__oxbasket $basket)
    {
        $products = [];
        foreach ($basket->getContents() as $basketItem) {
            $wrapping = null;
            if ($basketItem instanceof \oxOrderArticle) {
                $products[] = $this->fromOrderArticle($basketItem);
                $wrapping = $basketItem->getWrapping();
            } elseif ($basketItem instanceof \oxBasketItem) {
                $products[] = $this->fromBasketItem($basketItem);
                $wrapping = $basketItem->getWrapping();
                $products = array_merge($products, $this->getDiscountsFromBasketItem($basket, $basketItem));
            }
            if (!is_null($wrapping)) {
                $products[] = $this->fromWrapping($wrapping);
            }
        }
        if (!is_null($basket->getCard())) {
            $products[] = $this->fromWrapping($basket->getCard());
        }
        return array_merge($products, $this->getVouchers($basket), $this->getDiscountsFromBasket($basket));
    }

    /**
     * @param \mo_etracker__oxbasket $basket
     * @return stdClass
     */
    public function fromBasket(\mo_etracker__oxbasket $basket)
    {
        $etrackerBasket = new stdClass();
        $etrackerBasket->id = $basket->mo_etracker__getBasketId();
        $etrackerBasket->products = $this->getBasketItems($basket);
        return $etrackerBasket;
    }

    /**
     * @param \oxOrder $order
     * @return 'lead'|'sale'|'cancellation'|'partial_cancellation'
     */
    protected function determineOrderStatus(\oxOrder $order)
    {
        if ($order->oxorder__oxstorno->value == 1) {
            return 'cancellation';
        }
        foreach ($order->getOrderArticles() as $article) {
            /** @var oxOrderArticle $article */
            if ($article->oxorderarticles__oxstorno->value == 1) {
                return 'partial_cancellation';
            }
        }

        // This function is only called for new orders. Therefore we return 'lead' instead of 'sale'.
        return 'lead';
    }

    /**
     * Returns an array containing country, [state], city.
     *
     * @param oxOrder $order
     * @return string[]
     */
    protected function getCustomerInformation(\oxOrder $order)
    {
        /** @var oxAddress $deliveryAddress */
        $deliveryAddress = $order->getDelAddressInfo();
        if (is_null($deliveryAddress)) {
            return $this->getInvoiceInformation($order);
        }

        return array_filter([
            $order->getDelCountry(),
            $deliveryAddress->getStateTitle(),
            $deliveryAddress->oxaddress__oxcity->value
        ]);
    }

    /**
     * Returns an array containing country, [state], city.
     *
     * @param oxOrder $order
     * @return string[]
     */
    protected function getInvoiceInformation(\oxOrder $order)
    {
        $invoiceInformation = [$order->getBillCountry()];
        if (!empty($order->oxorder__oxbillstateid->value)) {
            $invoiceInformation[] = \oxNew('oxAddress')->getStateTitle($order->oxorder__oxbillstateid->value);
        }
        $invoiceInformation[] = $order->oxorder__oxbillcity->value;
        return $invoiceInformation;
    }

    /**
     * Returns the product id of each basket item that is wrapped.
     *
     * @param mo_etracker__oxbasket $basket
     * @return string[]
     */
    protected function getWrappedProductIds(\mo_etracker__oxbasket $basket)
    {
        $productIds = [];
        foreach ($basket->getContents() as $basketItem) {
            /** @var \oxBasketItem $basketItem */
            if (is_null($basketItem->getWrapping())) {
                continue;
            }
            $productIds[] = $this->determineProductId($basketItem->getArticle());
        }
        return $productIds;
    }

    /**
     * Returns the customer group; for now it returns a comma-separated list of each of the user's groups.
     *
     * @param oxUser $user
     * @return string
     */
    protected function getCustomerGroup(\oxUser $user)
    {
        $names = [];
        foreach ($user->getUserGroups() as $group) {
            $names[] = $group->oxgroups__oxtitle->value;
        }
        sort($names);
        return implode(',', $names);
    }

    /**
     * Returns a comma-separated list of applied coupon codes.
     *
     * It also considers the length of the coupon codes. If comma-separated list of the coupon codes exceeds 50,
     * then a sublist of coupon codes with at most 50 characters is returned.
     *
     * @param \mo_etracker__oxbasket $basket
     * @return string
     */
    protected function getCouponCodes(\mo_etracker__oxbasket $basket)
    {
        $couponCodes = implode(',', array_map(function ($voucher) {
            return $voucher->sVoucherNr;
        }, $basket->getVouchers()));

        if (strlen($couponCodes) <= 50) {
            return $couponCodes;
        }
        $lastOccurenceOfSeparator = strrpos(substr($couponCodes, 0, 50), ',');
        return substr($couponCodes, 0, $lastOccurenceOfSeparator);
    }

    /**
     * Enriches the order with optional information.
     *
     * @param oxOrder $order
     * @param mo_etracker__oxbasket $basket
     * @param stdClass $etrackerOrder
     */
    protected function enrichOrderWithOptionalInformation(
        \oxOrder $order,
        \mo_etracker__oxbasket $basket,
        stdClass $etrackerOrder
    ) {
        $etrackerOrder->customerId = $order->getOrderUser()->getId();
        $etrackerOrder->customerGroup = $this->getCustomerGroup($order->getOrderUser());
        $etrackerOrder->customerAddress = implode(',', $this->getCustomerInformation($order));
        $etrackerOrder->invoiceAddress = implode(',', $this->getInvoiceInformation($order));
        $etrackerOrder->paymentMethod = $order->getPaymentType()->oxpayments__oxdesc->value;
        $etrackerOrder->shipType = $order->getDelSet()->oxdeliveryset__oxtitle->value;
        $etrackerOrder->shipCosts = (string)$order->getOrderDeliveryPrice()->getPrice();
        $etrackerOrder->couponCodes = $this->getCouponCodes($basket);
        $etrackerOrder->giftPackage = $this->getWrappedProductIds($basket);
        // We cannot supply meaningful information about paymentConditions and deliveryConditions.
    }

    /**
     * @param \oxOrder $order
     * @param \mo_etracker__oxbasket $basket
     * @return stdClass
     */
    public function fromOrder(\oxOrder $order, \mo_etracker__oxbasket $basket)
    {
        $etrackerOrder = new stdClass();
        $etrackerOrder->orderNumber = (string)$order->oxorder__oxordernr->value;
        $etrackerOrder->status = $this->determineOrderStatus($order);
        $etrackerOrder->orderPrice = (string)$order->oxorder__oxtotalordersum->value; // TODO: gross/net price?
        $etrackerOrder->currency = $order->oxorder__oxcurrency->value;
        $etrackerOrder->basket = $this->fromBasket($basket);
        $etrackerOrder->differenceData = (int)true; // We always transmit the entire basket.
        $this->enrichOrderWithOptionalInformation($order, $basket, $etrackerOrder);
        $this->removeEmptyProperties($etrackerOrder);
        return $etrackerOrder;
    }

}
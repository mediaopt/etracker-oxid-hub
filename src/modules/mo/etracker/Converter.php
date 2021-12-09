<?php

namespace Mediaopt\Etracker;

use Mediaopt\Etracker\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Application\Model\Discount;
use OxidEsales\Eshop\Application\Model\DiscountList;
use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\OrderArticle;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\Voucher;
use OxidEsales\Eshop\Application\Model\Wrapping;
use OxidEsales\Eshop\Core\Registry;
use stdClass;

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
class Converter
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
                unset($object->{$property});
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
     * @param Article $article
     * @return string[]
     */
    protected function getCategories(Article $article)
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
     * @param Article $product
     * @return stdClass
     */
    protected function getVariant(Article $product)
    {
        $variants = new stdClass();
        if (!$product->isVariant()) {
            return $variants;
        }

        // We can transmit up to three variants. For now, we choose the first three.
        return (object)array_slice($this->extractVariants($product), 0, 3, true);
    }

    /**
     * @param Article $product
     * @return string[]
     */
    protected function extractVariants(Article $product)
    {
        // The | is magic, but this should not pose any problem.
        $names = explode('|', $product->getParentArticle()->oxarticles__oxvarname);
        $values = explode('|', $product->oxarticles__oxvarselect);
        $variantsArray = [];
        foreach (range(0, min(count($names), count($values))) as $i) {
            $name = trim($names[$i]);
            if (!empty($name)) {
                $variantsArray[$name] = trim($values[$i]);
            }
        }
        return $variantsArray;
    }

    /**
     * @param Article $article
     * @param stdClass $etrackerProduct
     */
    protected function enrichProductWithManufacturer(Article $article, stdClass $etrackerProduct)
    {
        $manufacturer = $article->getManufacturer();
        if ($manufacturer instanceof Manufacturer) {
            $etrackerProduct->pro_name = $manufacturer->getTitle();
        }
    }

    /**
     * @param Article $article
     * @param stdClass $etrackerProduct
     */
    protected function enrichProductWithEAN(Article $article, stdClass $etrackerProduct)
    {
        if (!empty($article->oxarticles__oxean->value)) {
            $etrackerProduct->ean = $article->oxarticles__oxean->value;
        }
    }

    /**
     * @param Article $article
     * @param stdClass $etrackerProduct
     */
    protected function enrichProduct(Article $article, stdClass $etrackerProduct)
    {
        $this->enrichProductWithManufacturer($article, $etrackerProduct);
        $this->enrichProductWithEAN($article, $etrackerProduct);
    }

    /**
     * Determines the product id to be used for a product.
     *
     * @param Article|OrderArticle $article
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
     * @param Article|OrderArticle $article
     * @return stdClass
     */
    public function fromArticle($article)
    {
        if ($article instanceof OrderArticle) {
            $actualArticle = oxNew(Article::class);
            $actualArticle->load($article->oxorderarticles__oxartid->value);
            return $this->fromArticle($actualArticle);
        }

        $etrackerProduct = new stdClass();
        $etrackerProduct->id = $this->determineProductId($article);
        $etrackerProduct->name = $article->oxarticles__oxtitle->value;
        $etrackerProduct->category = $this->getCategories($article);
        $etrackerProduct->price = (string)$article->getPrice()->getPrice();
        // TODO: gross/net price?
        $etrackerProduct->currency = Registry::getConfig()->getActShopCurrencyObject()->name;
        $etrackerProduct->variants = $this->getVariant($article);
        $this->enrichProduct($article, $etrackerProduct);
        return $etrackerProduct;
    }

    /**
     * Returns a basket item containing the information of the supploed basket item.
     *
     * @param BasketItem $basketItem
     * @return stdClass
     */
    public function fromBasketItem(BasketItem $basketItem)
    {
        $etrackerBasketItem = new stdClass();
        $etrackerBasketItem->product = $this->fromArticle($basketItem->getArticle());
        $etrackerBasketItem->quantity = $basketItem->getAmount();
        return $etrackerBasketItem;
    }

    /**
     * Returns a basket item containing the information of the supplied order article.
     *
     * @param OrderArticle $orderArticle
     * @return stdClass
     */
    public function fromOrderArticle(OrderArticle $orderArticle)
    {
        $etrackerBasketItem = new stdClass();
        $etrackerBasketItem->product = $this->fromArticle($orderArticle);
        $etrackerBasketItem->quantity = (int)$orderArticle->oxorderarticles__oxamount->value;
        return $etrackerBasketItem;
    }

    /**
     * Returns a basket item containing the information about the supplied wrapping.
     *
     * @param Wrapping $wrapping
     * @return stdClass
     */
    protected function fromWrapping(Wrapping $wrapping)
    {
        $etrackerBasketItem = new stdClass();
        $etrackerBasketItem->product = new stdClass();
        $etrackerBasketItem->product->id = $wrapping->getId();
        $etrackerBasketItem->product->name = $wrapping->oxwrapping__oxname->value;
        $etrackerBasketItem->product->category = strcasecmp($wrapping->oxwrapping__oxtype->value, 'WRAP') === 0 ? ['Wrapping'] : ['Gift Card'];
        $etrackerBasketItem->product->price = (string)$wrapping->getPrice();
        $etrackerBasketItem->product->currency = Registry::getConfig()->getActShopCurrencyObject()->name;
        $etrackerBasketItem->product->variants = new stdClass();
        $etrackerBasketItem->quantity = 1;
        return $etrackerBasketItem;
    }

    /**
     * Returns the vouchers (as basket item) used in the supplied basket.
     *
     * @param Basket $basket
     * @return stdClass[]
     */
    protected function getVouchers(Basket $basket)
    {
        $products = [];
        foreach ($basket->getVouchers() as $voucherObject) {
            $voucher = oxNew(Voucher::class);
            $voucher->load($voucherObject->sVoucherId);
            $etrackerBasketItem = new stdClass();
            $etrackerBasketItem->product = new stdClass();
            $etrackerBasketItem->product->id = $voucherObject->sVoucherNr;
            $etrackerBasketItem->product->name = $voucher->getSerie()->oxvoucherseries__oxserienr->value;
            $etrackerBasketItem->product->category = ['Voucher'];
            $etrackerBasketItem->product->price = '-' . $voucherObject->dVoucherdiscount;
            $etrackerBasketItem->product->currency = Registry::getConfig()->getActShopCurrencyObject()->name;
            $etrackerBasketItem->product->variants = new stdClass();
            $etrackerBasketItem->quantity = 1;
            $products[] = $etrackerBasketItem;
        }
        return $products;
    }

    /**
     * Returns the discounts (as basket item) of the supplied basket.
     *
     * @param Basket $basket
     * @return stdClass[]
     */
    protected function getDiscountsFromBasket(Basket $basket)
    {
        $products = [];
        foreach ((array)$basket->getDiscounts() as $discount) {
            $etrackerBasketItem = new stdClass();
            $etrackerBasketItem->product = new stdClass();
            $etrackerBasketItem->product->id = $discount->sOXID;
            $etrackerBasketItem->product->name = $discount->sDiscount;
            $etrackerBasketItem->product->category = ['Discount', 'Basket'];
            $etrackerBasketItem->product->price = '-' . $discount->dDiscount;
            $etrackerBasketItem->product->currency = Registry::getConfig()->getActShopCurrencyObject()->name;
            $etrackerBasketItem->product->variants = new stdClass();
            $etrackerBasketItem->quantity = 1;
            $products[] = $etrackerBasketItem;
        }
        return $products;
    }

    /**
     * Returns the discounts (as basket item) of the supplied basket item.
     *
     * @param Basket $basket
     * @param BasketItem $basketItem
     * @return stdClass[]
     */
    protected function getDiscountsFromBasketItem(Basket $basket, BasketItem $basketItem)
    {
        $initialPrice = $basketItem->getArticle()->getPrice();
        $previousPrice = $initialPrice->getPrice();
        $products = [];
        foreach (oxNew(DiscountList::class)->getBasketItemDiscounts($basketItem->getArticle(), $basket) as $discount) {
            /** @var Discount $discount */
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
            $etrackerBasketItem->product->currency = Registry::getConfig()->getActShopCurrencyObject()->name;
            $etrackerBasketItem->product->variants = new stdClass();
            $etrackerBasketItem->quantity = 1;
            $products[] = $etrackerBasketItem;
        }
        return $products;
    }

    /**
     * Returns the items of a basket.
     *
     * @param Basket $basket
     * @return stdClass[]
     */
    protected function getBasketItems(Basket $basket)
    {
        $products = [];
        foreach ($basket->getContents() as $basketItem) {
            $wrapping = null;
            if ($basketItem instanceof OrderArticle) {
                $products[] = $this->fromOrderArticle($basketItem);
                $wrapping = $basketItem->getWrapping();
            } elseif ($basketItem instanceof BasketItem) {
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
     * @param Basket $basket
     * @return stdClass
     */
    public function fromBasket(Basket $basket)
    {
        $etrackerBasket = new stdClass();
        $etrackerBasket->id = $basket->mo_etracker__getBasketId();
        $etrackerBasket->products = $this->getBasketItems($basket);
        return $etrackerBasket;
    }

    /**
     * @param Order $order
     * @return 'lead'|'sale'|'cancellation'|'partial_cancellation'
     */
    protected function determineOrderStatus(Order $order)
    {
        if ($order->oxorder__oxstorno->value == 1) {
            return 'cancellation';
        }
        foreach ($order->getOrderArticles() as $article) {
            /** @var OrderArticle $article */
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
     * @param Order $order
     * @return string[]
     */
    protected function getCustomerInformation(Order $order)
    {
        /** @var Address $deliveryAddress */
        $deliveryAddress = $order->getDelAddressInfo();
        if (is_null($deliveryAddress)) {
            return $this->getInvoiceInformation($order);
        }

        return array_filter([$order->getDelCountry(), $deliveryAddress->getStateTitle(), $deliveryAddress->oxaddress__oxcity->value]);
    }

    /**
     * Returns an array containing country, [state], city.
     *
     * @param Order $order
     * @return string[]
     */
    protected function getInvoiceInformation(Order $order)
    {
        $invoiceInformation = [$order->getBillCountry()];
        if (!empty($order->oxorder__oxbillstateid->value)) {
            $invoiceInformation[] = oxNew(Address::class)->getStateTitle($order->oxorder__oxbillstateid->value);
        }
        $invoiceInformation[] = $order->oxorder__oxbillcity->value;
        return $invoiceInformation;
    }

    /**
     * Returns the product id of each basket item that is wrapped.
     *
     * @param Basket $basket
     * @return string[]
     */
    protected function getWrappedProductIds(Basket $basket)
    {
        $productIds = [];
        foreach ($basket->getContents() as $basketItem) {
            /** @var BasketItem $basketItem */
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
     * @param User $user
     * @return string
     */
    protected function getCustomerGroup(User $user)
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
     * @param Basket $basket
     * @return string
     */
    protected function getCouponCodes(Basket $basket)
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
     * @param Order $order
     * @param Basket $basket
     * @param stdClass $etrackerOrder
     */
    protected function enrichOrderWithOptionalInformation(Order $order, Basket $basket, stdClass $etrackerOrder)
    {
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
     * @param Order $order
     * @param Basket $basket
     * @return stdClass
     */
    public function fromOrder(Order $order, Basket $basket)
    {
        $etrackerOrder = new stdClass();
        $etrackerOrder->orderNumber = (string)$order->oxorder__oxordernr->value;
        $etrackerOrder->status = $this->determineOrderStatus($order);
        $etrackerOrder->orderPrice = (string)$order->oxorder__oxtotalordersum->value;
        // TODO: gross/net price?
        $etrackerOrder->currency = $order->oxorder__oxcurrency->value;
        $etrackerOrder->basket = $this->fromBasket($basket);
        $etrackerOrder->differenceData = (int)true;
        // We always transmit the entire basket.
        $this->enrichOrderWithOptionalInformation($order, $basket, $etrackerOrder);
        $this->removeEmptyProperties($etrackerOrder);
        return $etrackerOrder;
    }
}

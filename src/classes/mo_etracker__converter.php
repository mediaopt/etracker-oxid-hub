<?php

/**
 *
 * TODO: Be consistent w.r.t. article/product
 *
 * @author derksen mediaopt GmbH
 */
class mo_etracker__converter
{

    /**
     * @param oxArticle $article
     * @return string[]
     */
    protected function getCategories(\oxArticle $article)
    {
        // TODO: We have to choose at most four categories.
        $categories = [];
        $parent = $article->getCategory();
        do {
            array_unshift($categories, $parent->getTitle());
            $parent = $parent->getParentCategory();
        } while (!is_null($parent));
        return $categories;
    }

    /**
     * @param $product
     * @return stdClass
     */
    protected function getVariant(\oxArticle $product)
    {
        $variants = new stdClass();
        if (!$product->isVariant()) {
            return $variants;
        }

        // TODO: The string ' | ' is magic.
        $variantNames = explode(' | ', $product->oxarticles__oxvarname);
        $selectedVariants = explode(' | ', $product->oxarticles__oxvarselect);
        assert(count($variantNames) === count($selectedVariants));

        // TODO: We have to select at most three variants; which ones do we select?
        for ($i = 0; $i < min(3, count($variantNames)); $i++) {
            $variants->{$variantNames[$i]} = $selectedVariants[$i];
        }
        return $variants;
    }

    /**
     * @param oxArticle $product
     * @param $etrackerProduct
     */
    protected function enrichProductWithManufacturer(\oxArticle $product, $etrackerProduct)
    {
        /** @var oxManufacturer $manufacturer */
        $manufacturer = $product->getManufacturer();
        if (!is_null($manufacturer)) {
            $etrackerProduct->pro_name = $manufacturer->getTitle();
        }
    }

    /**
     * @param oxArticle $product
     * @param $etrackerProduct
     */
    protected function enrichProductWithEAN(\oxArticle $product, $etrackerProduct)
    {
        if (!empty($product->oxarticles__oxean->value)) {
            $etrackerProduct->ean = $product->oxarticles__oxean->value;
        }
    }

    /**
     * @param \oxArticle $product
     * @param stdClass $etrackerProduct
     */
    protected function enrichProduct(\oxArticle $product, $etrackerProduct)
    {
        $this->enrichProductWithManufacturer($product, $etrackerProduct);
        $this->enrichProductWithEAN($product, $etrackerProduct);
    }

    /**
     * @param \oxArticle $product
     * @return string
     */
    protected function determineProductId(\oxArticle $product)
    {
        if (!empty($product->oxarticles__oxartnum->value)) {
            return $product->oxarticles__oxartnum->value;
        }
        return $product->getProductId();
    }

    /**
     * @param \oxArticle $product
     * @return stdClass
     */
    public function fromProduct(\oxArticle $product)
    {
        $etrackerProduct = new stdClass();
        $etrackerProduct->id = $this->determineProductId($product);
        $etrackerProduct->name = $product->oxarticles__oxtitle->value;
        $etrackerProduct->category = $this->getCategories($product);
        $etrackerProduct->price = (string)$product->getPrice()->getPrice(); // TODO: gross/net price?
        $etrackerProduct->currency = \oxRegistry::getConfig()->getActShopCurrencyObject()->name;
        $etrackerProduct->variants = $this->getVariant($product);
        $this->enrichProduct($product, $etrackerProduct);
        return $etrackerProduct;
    }

    /**
     * TODO: Implement.
     *
     * @param $basketItem
     * @return stdClass
     */
    public function fromBasketItem($basketItem)
    {
        $etrackerBasketItem = new stdClass();
        $etrackerBasketItem->product = null; // TODO: Use fromProduct
        $etrackerBasketItem->quantity = 0;
        return $etrackerBasketItem;
    }

    /**
     * TODO: Implement.
     *
     * @param $basket
     * @return stdClass
     */
    public function fromBasket($basket)
    {
        $etrackerBasket = new stdClass();
        $etrackerBasket->id = (string)null;
        $etrackerBasket->products = []; // TODO: use fromBasketItem
        return $etrackerBasket;
    }

    /**
     * TODO: Implement.
     *
     * @param $order
     * @return 'lead'|'sale'|'cancellation'|'partial_cancellation'
     */
    protected function determineOrderStatus($order)
    {
        return '';
    }

    /**
     * TODO: Implement.
     *
     * @param $order
     * @return stdClass
     */
    protected function getBasket($order)
    {
        // TODO: use fromBasket
        return new stdClass();
    }

    protected function enrichOrder($order, $etrackerOrder)
    {
        $etrackerOrder->customerId = (string)null; // optional
        $etrackerOrder->customerGroup = (string)null; // optional
        $etrackerOrder->customerAddress = (string)null; // optional
        $etrackerOrder->invoiceAddress = (string)null; // optional
        $etrackerOrder->paymentMethod = (string)null; // optional
        $etrackerOrder->deliveryConditions = (string)null; // optional
        $etrackerOrder->paymentConditions = (string)null; // optional
        $etrackerOrder->shipType = (string)null; // optional
        $etrackerOrder->shipCosts = (string)null; // optional
        $etrackerOrder->couponCodes = (string)null; // optional
        $etrackerOrder->giftPackage = []; // optional, enthält Produkt-IDs
        return $etrackerOrder;
    }

    /**
     * TODO: Implement.
     *
     * @param $order
     * @return stdClass
     */
    public function fromOrder($order)
    {
        $etrackerOrder = new stdClass();
        $etrackerOrder->orderNumber = (string)null;
        $etrackerOrder->status = $this->determineOrderStatus($order);
        $etrackerOrder->orderPrice = (string)null;
        $etrackerOrder->currency = (string)null; // ISO 4217
        $etrackerOrder->basket = $this->getBasket($order);
        $etrackerOrder->differenceData = true; // optional, vollständig
        return $etrackerOrder;
    }

}
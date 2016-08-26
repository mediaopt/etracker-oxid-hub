<?php

/**
 * helper functions
 */
class mo_etracker__helper
{
    /**
     * prepend path
     *
     * @param string pagename
     * @param string path
     * @return string
     */
    public function moetPrependPath($pagename, $path)
    {
        if (!preg_match('#(/|_|^)' . preg_quote($pagename) . '/*$#', $path)) {
            $path .= $pagename;
        }
        return $path;
    }

    /**
     * process thankyou data
     * @param array data
     *
     * @return string
     */
    public function moetProcessThankyouData($data)
    {
        $data['et_target'] .= isset($data['et_tval']) ? ',' . $data['et_tval'] : ',';
        $data['et_target'] .= isset($data['et_tonr']) ? ',' . $data['et_tonr'] : ',';
        $data['et_target'] .= isset($data['et_tsale']) ? ',' . $data['et_tsale'] : ',';
        unset($data['et_tval']);
        unset($data['et_tonr']);
        unset($data['et_tsale']);

        return $data;
    }

    /**
     * process shop url
     *
     * @param string shopUrl
     * @return string
     */
    public function moetProcessShopUrl($shopUrl)
    {
        //cut off prefixes if they are already in shop url (http://myurl.com/shop
        if (preg_match('#^http://[^/]+/(.+)$#', $shopUrl, $matches)) {
            return preg_replace('#' . preg_quote($matches[1]) . '$#', '', $shopUrl);
        }

        return $shopUrl;
    }


    /**
     * get category entry
     * @param $category
     * @param $view
     *
     * @return string
     */
    public function moetGetCategoryEntry($category, $view)
    {
        $prefixClasses = ['aList', 'VendorList', 'Tag', 'RecommList', 'ManufacturerList', 'Details'];
        $subject = $category->oxcategories__oxtitle->value;
        foreach ($prefixClasses as $className) {
            if (is_a($view, $className)) {
                $translation = oxRegistry::getLang()->translateString('MOET_PREFIX_' . strtoupper($view->getClassName()));
                $subject = $translation . $subject;
                break;
            }
        }
        return $this->moetEscapeCharacters($subject) . '/';
    }

    /**
     * escape slashes for etracker
     *
     * @param string $string
     */
    public function moetEscapeCharacters($string)
    {
        return str_replace(['/', '.', ','], '', $string);
    }

    /**
     * escape etracker vars
     * @param array $etrackerVars
     *
     * @return string
     */
    public function moetEscapeValues($etrackerVars)
    {
        $isAlreadyEscaped = ['et_basket'];
        foreach ($etrackerVars as $key => $value) {
            if(in_array($key, $isAlreadyEscaped)) {
                continue;
            }
            $etrackerVars[$key] = rawurlencode($value);
        }
        return $etrackerVars;
    }

    /**
     * replace separator chars
     * @param string $string
     */
    public function moetCleanBasketString($string)
    {
        return rawurlencode(str_replace([',', ';'], '.', $string));
    }

    /**
     * serialize tracking params
     * @param string $string
     * @return string
     */
    public function moetSerializeData($data)
    {
        $output = '';

        foreach ($data as $key => $value) {
            $output .= '&amp;' . $key . '=' . $value;
        }
        return $output;
    }

    /**
     * adds MOET_ prefix to upperstring ident and checks for avail translation, otherwise return ident
     *
     * @param string $identifier
     * @return string if possible, a translation based on the identifier; otherwise the identifier
     */
    public function moetTranslate($identifier)
    {
        $translationKey = 'MOET_' . strtoupper($identifier);
        $translation = oxRegistry::getLang()->translateString($translationKey);
        return $translation !== $translationKey ? $translation : $identifier;
    }

    /**
     * build string containing basketcontent
     *
     * @return string
     */
    public function moetBuildBasketString($basket)
    {
        $etBasket = '';
        foreach ($basket->getContents() as $basketItem) {
            $product = $basketItem->getArticle();
            $etBasket .= $this->moetCleanBasketString($product->oxarticles__oxid->value) . ',';
            $etBasket .= $this->moetCleanBasketString($product->oxarticles__oxtitle->value) . ',';
            $etBasket .= $this->moetCleanBasketString($product->getCategory()->oxcategories__oxtitle->value) . ',';
            $etBasket .= $this->moetCleanBasketString($basketItem->getAmount()) . ',';
            $etBasket .= number_format($basketItem->getPrice()->getNettoPrice(), 2);
            $etBasket .= ';';
        }
        return $etBasket;
    }
}
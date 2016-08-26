<?php

/**
 * $Id: mo_etracker__oxviewconfig.php 59 2012-05-04 08:03:11Z martin $
 */

/**
 * @extend oxviewconfig
 * adds etracker functionality to viewconfig-object
 */
class mo_etracker__oxviewconfig extends mo_etracker__oxviewconfig_parent
{

    public $checkoutViews = array('Basket', 'User', 'Payment', 'Order', 'Thankyou');
    protected $moetView;
    protected $moetOrder;
    protected $moetBasket;
    protected $moetRoot;
    protected $myConfig;
    protected $etrackerVars = null;

    /**
     * get etracker vars
     *
     * @return array
     */
    public function moetGetEtrackerVars()
    {
        if ($this->etrackerVars !== null) {
            return $this->etrackerVars;
        }

        $helper = mo_etracker__main::getInstance()->getHelper();
        $myConfig = $this->getConfig();

        $this->moetView = $myConfig->getActiveView();
        $this->moetRoot = oxRegistry::getConfig()->getConfigParam('moetroot');

        $etrackerVars = array();

        $etrackerVars['et_pagename'] = $this->moetGetPagename();
        $etrackerVars['et_areas'] = $this->moetGetAreas();
        $etrackerVars['et_url'] = $this->moetGetUrl();
        $etrackerVars['et_target'] = $this->moetGetTarget();

        $etrackerVars['et_se'] = oxRegistry::getConfig()->getConfigParam('moetsechannel');

        if (is_a($this->moetView, 'Thankyou')) {
            $this->order = $this->moetView->getOrder();
            $this->basket = $this->moetView->getBasket();

            $etrackerVars['et_tval'] = $this->moetGetTval();
            $etrackerVars['et_tonr'] = $this->moetGetTonr();
            $etrackerVars['et_tsale'] = $this->moetGetTsale();
            $etrackerVars['et_cust'] = $this->moetGetCust();
            $etrackerVars['et_basket'] = $this->moetGetBasket();
        }

        $etrackerVars = $helper->moetEscapeValues($etrackerVars);

        //no escaping here
        $etrackerVars['et_tag'] = 'language=' . $this->moetGetLanguageAbbr();

        return $this->etrackerVars = $etrackerVars;
    }

    /**
     * get etracker vars
     *
     * @return array
     */
    public function mo_etracker__getConfiguration()
    {
        $etrackerConfig = [];
        foreach(['moetsecurecode', 'moetsecurekey', 'moetsechannel', 'moetroot', 'moetdebug'] as $configurationKey) {
            $etrackerConfig[$configurationKey] = \oxRegistry::getConfig()->getConfigParam($configurationKey);
        }
        return $etrackerConfig;
    }

    /**
     * get noscript vars
     * @param array data
     *
     * @return string
     */
    public function moetGetNoscriptVars($data)
    {
        $helper = mo_etracker__main::getInstance()->getHelper();

        //append tval, tonr & tsale to target param (only avail on last step)
        if ($this->moetView instanceof Thankyou) {
            $data = $helper->moetProcessThankyouData($data);
        }

        return $helper->moetSerializeData($data);
    }

    /**
     * get pagename
     *
     * @return string
     */
    protected function moetGetPagename()
    {
        $myConfig = $this->getConfig();
        /*@var $helper mo_etracker__helper */
        $helper = mo_etracker__main::getInstance()->getHelper();
        $pagename = '';

        $sShopURL = $myConfig->getConfigParam('sShopURL');
        if (!$uri = $helper->moetProcessShopUrl($sShopURL)) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        $pagename = $this->moetHandlePagename($uri);

        //prepend path
        if ($path = $this->moetGetPath()) {
            $pagename = $helper->moetPrependPath($pagename, $path);
        }

        //on empty pagename, translate view
        if (!$pagename) {
            $pagename = $this->moetGetViewInformation();
        }

        //prepend root
        $pagename = $this->moetGetRoot() . $pagename;
        $pagename = rtrim($pagename, '/');

        //special case for index
        if ($this->getActiveClassName() == 'start') {
            $pagename = '__INDEX__' . $pagename;
        }

        return $pagename;
    }

    /**
     * handle pagename
     * @param $uri
     *
     * @return mixed
     */
    protected function moetHandlePagename($uri)
    {
        /*@var $helper mo_etracker__helper */
        $helper = mo_etracker__main::getInstance()->getHelper();
        $myConfig = $this->getConfig();

        if ($this->is404Call()) {
            return '404';
        }

        if (preg_match('#/([^/]+)?(/|\.html.*)$#', $uri, $matches)) {
            //remove special characters
            return $helper->moetEscapeCharacters($matches[1]);
        }

        if (($baseview = $myConfig->getRequestParameter('cl')) && ($baseview != 'start')) {
            //special handling for start-view => empty pagename
            return $helper->moetTranslate($baseview);
        }

        return $uri;
    }

    /**
     * get areas
     *
     * @return string
     */
    protected function moetGetAreas()
    {
        /*@var $helper mo_etracker__helper */
        $helper = mo_etracker__main::getInstance()->getHelper();
        $areas = '';

        //root
        $areas .= $this->moetGetRoot();

        if ($this->moetGetCheckoutStep() !== false) {
            return $areas . 'Checkout';
        }

        //path (categories, manufacturer...)
        if ($path = $this->moetGetPath()) {
            $areas .= $path;

            //append class name to path
            $areas .= $helper->moetTranslate($this->getActiveClassName());

            $areas = rtrim($areas, '/') . ',';

            //add root as prefix (//)
            $areas .= $this->moetGetRoot(false);
        }

        $areas .= $this->moetGetViewInformation();

        return $areas;
    }

    /**
     * rewrite urls if method is post
     *
     * @return unknown
     */
    protected function moetGetUrl()
    {
        if (empty($_POST)) {
            return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $query = array();
            if (!empty($_POST['cl'])) {
                $query['cl'] = $_POST['cl'];
            }
            if (!empty($_POST['fnc'])) {
                $query['fnc'] = $_POST['fnc'];
            }

            return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . http_build_query($query);
        }
    }

    /**
     * build target path
     *
     * @return string
     */
    protected function moetGetTarget()
    {
        $target = '';

        $checkoutStep = $this->moetGetCheckoutStep();
        //checkout-target chain
        if ($checkoutStep !== false) {
            $target .= $this->moetGetRoot(true);
            $target .= $this->moetGetCheckoutTargetChain($checkoutStep);
        }
        return $target;
    }

    /**
     * build target path in checkout-area
     *
     * @param $checkoutstep
     * @return string
     */
    protected function moetGetCheckoutTargetChain($checkoutStep)
    {
        $helper = mo_etracker__main::getInstance()->getHelper();
        $target = '';

        for ($i = 0; $i <= $checkoutStep; $i++) {
            //replace target-separator with "-"
            $target .= $helper->moetTranslate($this->checkoutViews[$i]) . '/';
        }
        $target = rtrim($target, '/');

        return $target;
    }

    /**
     * check if in checkout-area and return checkoutstep
     *
     * @return mixed
     */
    protected function moetGetCheckoutStep()
    {
        foreach ($this->checkoutViews as $checkoutStep => $className) {
            if (is_a($this->moetView, $className)) {
                return $checkoutStep;
            }
        }
        return false;
    }

    /**
     * return product netto prices
     *
     * @return float
     */
    protected function moetGetTval()
    {
        return $this->basket->getNettoSum();
    }

    /**
     * return order-number
     *
     * @return string
     */
    protected function moetGetTonr()
    {
        return $this->order->oxorder__oxordernr->value;
    }

    /**
     * return sale-flag
     *
     * @return unknown
     */
    protected function moetGetTsale()
    {
        return 1;
    }

    /**
     * check if user has ordered sth before
     *
     * @return int
     */
    protected function moetGetCust()
    {
        $database = oxDb::getDb();

        //try via ID
        $sql = "SELECT IF(COUNT(oxid) > 1, 1, 0) as isKnownCustomer
            FROM oxorder
            WHERE
            oxuserid = " . $database->quote($this->order->oxorder__oxuserid->value);

        if (!$database->getOne($sql)) {
            //retry with email
            $sql = "SELECT IF(COUNT(oxid) > 1, 1, 0) as isKnownCustomer
              FROM oxorder
              WHERE
              oxbillemail = " . $database->quote($this->order->oxorder__oxbillemail->value);
            return $database->getOne($sql);
        }
        return 1;
    }

    /**
     * build string containing basketcontent
     *
     * @return string
     */
    protected function moetGetBasket()
    {
        $helper = mo_etracker__main::getInstance()->getHelper();

        return $helper->moetBuildBasketString($this->basket);
    }

    /**
     * check if all necessary config-parameters are set
     *
     */
    public function moetIsConfigComplete()
    {
        return (bool)oxRegistry::getConfig()->getConfigParam('moetsecurecode');
    }

    /**
     * get path
     *
     * @return string
     */
    protected function moetGetPath()
    {
        $helper = mo_etracker__main::getInstance()->getHelper();
        $path = '';

        //breadcrumb / category-path
        $path = $this->moetProcessPaths();

        //try to build the tree manually if other methods failed
        if (!$path && method_exists($this->moetView, 'getCategory')) {
            if ($category = $this->moetView->getCategory()) {
                $path = $helper->moetGetCategoryEntry($category, $this->moetView) . $path;
                while ($category = $category->getParentCategory()) {
                    $path = $helper->moetGetCategoryEntry($category, $this->moetView) . $path;
                }
            }
        }
        return $path;
    }

    /**
     * process paths
     *
     * @return string
     */
    protected function moetProcessPaths()
    {
        if (method_exists($this->moetView, 'getTreePath')) {
            return $this->moetProcessTreePath();
        }

        if (method_exists($this->moetView, 'getCatTreePath')) {
            return $this->moetProcessCatTreePath();
        }
    }

    /**
     * process treepath
     *
     * @return string
     */
    protected function moetProcessTreePath()
    {
        $helper = mo_etracker__main::getInstance()->getHelper();
        $path = '';

        foreach ($this->moetView->getTreePath() as $category) {
            $path .= $helper->moetGetCategoryEntry($category, $this->moetView);
        }

        return $path;
    }

    /**
     * process cattreepath
     *
     * @return string
     */
    protected function moetProcessCatTreePath()
    {
        $helper = mo_etracker__main::getInstance()->getHelper();
        $path = '';

        if ($tree = $this->moetView->getCatTreePath()) {
            foreach ($tree as $category) {
                $path .= $helper->moetGetCategoryEntry($category, $this->moetView);
            }
        }

        return $path;
    }

    /**
     * get root
     *
     * @return string
     */
    protected function moetGetRoot($asNode = true)
    {
        $root = '';

        if ($this->moetRoot) {
            $root = $this->moetRoot . '/';

            if (!$asNode) {
                $root .= '/';
            }
        }

        $root .= $this->moetAddLanguagPrefix($asNode);

        return $root;
    }

    protected function moetAddLanguagPrefix($asNode)
    {
        $prefix = $this->moetGetLanguageAbbr() . '/';
        if (!$asNode) {
            $prefix .= '/';
        }

        return $prefix;
    }

    public function moetGetLanguageAbbr()
    {
        $lang = $this->getConfig()->getActiveShop()->getLanguage();
        $oxLang = oxRegistry::get('oxLang');
        return $oxLang->getLanguageAbbr($lang);
    }


    /**
     * get active class and function
     *
     * @return string
     */
    protected function moetGetViewInformation()
    {
        $helper = mo_etracker__main::getInstance()->getHelper();
        $output = '';

        //class-name & function
        $output .= $helper->moetTranslate($this->getActiveClassName());

        //functions
        if ($fnc = $this->getConfig()->getRequestParameter('fnc')) {
            $output .= '/' . $helper->moetTranslate($fnc);
        }
        return $output;
    }

    /**
     * checks whether current call has led to a 404 page
     */
    protected function is404Call()
    {
        $myConfig = $this->getConfig();
        return get_class($myConfig->getActiveView()) == 'oxUBase';
    }

    public function mo_etracker__getEvents()
    {
        /* @TODO: Check UTF-8 */

        $product = new stdClass();
        $product->id = '3445';
        $product->name = 'Elfrieda';
        $product->category = ['Tiere', 'Großwild', 'Giraffen', 'Liebe Giraffen'];
        $product->price = '1723.60';
        $product->currency = 'EUR';
        $product->variants = new stdClass();
        $product->variants->Farbe = 'gelb';
        $product->variants->Geschlecht = 'weiblich';
        $product->variants->Figure = 'dünn';
        return json_encode([['sendEvent', 'viewProduct', $product, 'Warenkorb 1']]);
    }

}
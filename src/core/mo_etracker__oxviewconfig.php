<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */


/**
 * This class provides functionality, which is accessed via the templates.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend oxViewConfig
 */
class mo_etracker__oxviewconfig extends mo_etracker__oxviewconfig_parent
{

    public $mo_etracker__checkOutViews = array('Basket', 'User', 'Payment', 'Order', 'Thankyou');
    protected $mo_etracker__view;
    protected $mo_etracker__order;
    protected $mo_etracker__basket;
    protected $mo_etracker__root;
    protected $mo_etracker__config;
    protected $mo_etracker__vars = null;

    /**
     * get etracker vars
     *
     * @return array
     */
    public function mo_etracker__getVars()
    {
        if ($this->mo_etracker__vars !== null) {
            return $this->mo_etracker__vars;
        }

        $main = \oxRegistry::get('mo_etracker__main');
        $this->mo_etracker__view = $this->getConfig()->getActiveView();
        $this->mo_etracker__root = oxRegistry::getConfig()->getConfigParam('mo_etracker__root');

        $etrackerVars = array();
        $etrackerVars['et_pagename'] = $this->mo_etracker__getPageName();
        $etrackerVars['et_areas'] = $this->mo_etracker__getAreas();
        $etrackerVars['et_url'] = $this->mo_etracker__getUrl();
        $etrackerVars['et_target'] = $this->mo_etracker__getTarget();
        $etrackerVars['et_se'] = oxRegistry::getConfig()->getConfigParam('mo_etracker__sechannel');
        $etrackerVars = $main->escapeValues($etrackerVars);
        $etrackerVars['et_tag'] = 'language=' . $this->mo_etracker__getLanguageAbbr();

        $this->mo_etracker__vars = $etrackerVars;
        return $this->mo_etracker__vars;
    }

    /**
     * get etracker vars
     *
     * @return array
     */
    public function mo_etracker__getConfiguration()
    {
        $etrackerConfig = [];
        foreach (['securecode', 'securekey', 'sechannel', 'root', 'debug'] as $configurationKey) {
            $etrackerConfig['mo_etracker__' . $configurationKey] = \oxRegistry::getConfig()
                ->getConfigParam('mo_etracker__' . $configurationKey);
        }
        return $etrackerConfig;
    }

    /**
     * get noscript vars
     *
     * @param array $data
     * @return string
     */
    public function mo_etracker__getNoScriptVars($data)
    {
        return \oxRegistry::get('mo_etracker__main')->serializeData($data);
    }

    /**
     * get pagename
     *
     * @return string
     */
    protected function mo_etracker__getPageName()
    {
        //special case for index
        if ($this->getActiveClassName() === 'start') {
            return '__INDEX__';
        }

        $main = \oxRegistry::get('mo_etracker__main');
        $uri = $main->processShopUrl($this->getConfig()->getConfigParam('sShopURL')) ?: $_SERVER['REQUEST_URI'];
        $pagename = $this->mo_etracker__handlePageName($uri);

        //prepend path
        $path = $this->mo_etracker__getPath();
        if (!empty($path)) {
            $pagename = rtrim($pagename . '/' . $path, '/');
        }

        return rtrim($this->mo_etracker__getRoot() . $pagename . '/' . $this->mo_etracker__getViewInformation(), '/');
    }

    /**
     * handle pagename
     * @param $uri
     *
     * @return mixed
     */
    protected function mo_etracker__handlePageName($uri)
    {
        if ($this->mo_etracker__is404Call()) {
            return '404';
        }

        $main = \oxRegistry::get('mo_etracker__main');

        if (preg_match('#/([^/]+)?(/|\.html.*)$#', $uri, $matches)) {
            //remove special characters
            return $main->escapeCharacters($matches[1]);
        }

        $baseview = $this->getConfig()->getRequestParameter('cl');
        if (!empty($baseview) && $baseview !== 'start') {
            //special handling for start-view => empty pagename
            return $main->translate($baseview);
        }

        return $uri;
    }

    /**
     * get areas
     *
     * @return string
     */
    protected function mo_etracker__getAreas()
    {
        $areas = $this->mo_etracker__getRoot();
        if ($this->mo_etracker__getCheckoutStep() !== false) {
            return $areas . 'Checkout';
        }

        //path (categories, manufacturer...)
        $path = $this->mo_etracker__getPath();
        if ($path) {
            $areas .= $path;

            //append class name to path
            $areas .= \oxRegistry::get('mo_etracker__main')->translate($this->getActiveClassName());
            $areas = rtrim($areas, '/') . ',';

            //add root as prefix (//)
            $areas .= $this->mo_etracker__getRoot(false);
        }

        $areas .= $this->mo_etracker__getViewInformation();

        return $areas;
    }

    /**
     * rewrite urls if method is post
     *
     * @return unknown
     */
    protected function mo_etracker__getUrl()
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
    protected function mo_etracker__getTarget()
    {
        $target = '';

        $checkoutStep = $this->mo_etracker__getCheckoutStep();
        //checkout-target chain
        if ($checkoutStep !== false) {
            $target .= $this->mo_etracker__getRoot(true);
            $target .= $this->mo_etracker__getCheckoutTargetChain($checkoutStep);
        }
        return $target;
    }

    /**
     * build target path in checkout-area
     *
     * @param $checkoutstep
     * @return string
     */
    protected function mo_etracker__getCheckoutTargetChain($checkoutStep)
    {
        $target = '';
        for ($i = 0; $i <= $checkoutStep; $i++) {
            //replace target-separator with "-"
            $target .= \oxRegistry::get('mo_etracker__main')->translate($this->mo_etracker__checkOutViews[$i]) . '/';
        }
        $target = rtrim($target, '/');
        return $target;
    }

    /**
     * check if in checkout-area and return checkoutstep
     *
     * @return mixed
     */
    protected function mo_etracker__getCheckoutStep()
    {
        foreach ($this->mo_etracker__checkOutViews as $checkoutStep => $className) {
            if (is_a($this->mo_etracker__view, $className)) {
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
    protected function mo_etracker__getTval()
    {
        return $this->basket->getNettoSum();
    }

    /**
     * return order-number
     *
     * @return string
     */
    protected function mo_etracker__getTonr()
    {
        return $this->order->oxorder__oxordernr->value;
    }

    /**
     * return sale-flag
     *
     * @return unknown
     */
    protected function mo_etracker__getTsale()
    {
        return 1;
    }

    /**
     * check if user has ordered sth before
     *
     * @return int
     */
    protected function mo_etracker__getCust()
    {
        $database = oxDb::getDb();

        //try via ID
        $sql = "SELECT IF(COUNT(oxid) > 1, 1, 0) as isKnownCustomer
            FROM oxorder
            WHERE
            oxuserid = " . $database->quote($this->order->oxorder__oxuserid->value);

        if ($database->getOne($sql)) {
            return 1;
        }

        //retry with email
        $sql = "SELECT IF(COUNT(oxid) > 1, 1, 0) as isKnownCustomer
              FROM oxorder
              WHERE
              oxbillemail = " . $database->quote($this->order->oxorder__oxbillemail->value);
        return $database->getOne($sql);
    }

    /**
     * build string containing basketcontent
     *
     * @return string
     */
    protected function mo_etracker__getBasket()
    {
        return \oxRegistry::get('mo_etracker__main')->buildBasketString($this->basket);
    }

    /**
     * check if all necessary config-parameters are set
     *
     */
    public function mo_etracker__isConfigComplete()
    {
        return (bool)oxRegistry::getConfig()->getConfigParam('mo_etracker__securecode');
    }

    /**
     * get path
     *
     * @return string
     */
    protected function mo_etracker__getPath()
    {
        $main = \oxRegistry::get('mo_etracker__main');

        //breadcrumb / category-path
        $path = $this->mo_etracker__processPaths();

        //try to build the tree manually if other methods failed
        if (!$path && method_exists($this->mo_etracker__view, 'getCategory')) {
            if ($category = $this->mo_etracker__view->getCategory()) {
                $path = $main->getCategoryEntry($category, $this->mo_etracker__view) . $path;
                while ($category = $category->getParentCategory()) {
                    $path = $main->getCategoryEntry($category, $this->mo_etracker__view) . $path;
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
    protected function mo_etracker__processPaths()
    {
        if (method_exists($this->mo_etracker__view, 'getTreePath')) {
            return $this->mo_etracker__processTreePath();
        }

        if (method_exists($this->mo_etracker__view, 'getCatTreePath')) {
            return $this->mo_etracker__processCatTreePath();
        }
    }

    /**
     * process treepath
     *
     * @return string
     */
    protected function mo_etracker__processTreePath()
    {
        $path = '';
        foreach ($this->mo_etracker__view->getTreePath() as $category) {
            $path .= \oxRegistry::get('mo_etracker__main')->getCategoryEntry($category, $this->mo_etracker__view);
        }
        return $path;
    }

    /**
     * process cattreepath
     *
     * @return string
     */
    protected function mo_etracker__processCatTreePath()
    {
        $tree = $this->mo_etracker__view->getCatTreePath();
        if (!$tree) {
            return '';
        }
        $path = '';
        foreach ($tree as $category) {
            $path .= \oxRegistry::get('mo_etracker__main')->getCategoryEntry($category, $this->mo_etracker__view);
        }
        return $path;
    }

    /**
     * get root
     *
     * @return string
     */
    protected function mo_etracker__getRoot($asNode = true)
    {
        $root = '';

        if ($this->mo_etracker__root) {
            $root = $this->mo_etracker__root . '/';

            if (!$asNode) {
                $root .= '/';
            }
        }

        $root .= $this->mo_etracker__addLanguagPrefix($asNode);

        return $root;
    }

    protected function mo_etracker__addLanguagPrefix($asNode)
    {
        $prefix = $this->mo_etracker__getLanguageAbbr() . '/';
        if (!$asNode) {
            $prefix .= '/';
        }

        return $prefix;
    }

    public function mo_etracker__getLanguageAbbr()
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
    protected function mo_etracker__getViewInformation()
    {
        $main = \oxRegistry::get('mo_etracker__main');
        $output = '';

        //class-name & function
        $output .= $main->translate($this->getActiveClassName());

        //functions
        if ($fnc = $this->getConfig()->getRequestParameter('fnc')) {
            $output .= '/' . $main->translate($fnc);
        }
        return $output;
    }

    /**
     * checks whether current call has led to a 404 page
     */
    protected function mo_etracker__is404Call()
    {
        $myConfig = $this->getConfig();
        return get_class($myConfig->getActiveView()) == 'oxUBase';
    }

    /**
     * Returns true iff events have been queued.
     *
     * @return bool
     */
    public function mo_etracker__areEventsQueued()
    {
        return \oxRegistry::get('mo_etracker__main')->hasEvents();
    }

    /**
     * Transforms the queued events into events that can be transmitted to etracker.
     *
     * @return string
     */
    public function mo_etracker__getEventCalls()
    {
        $calls = [];
        foreach (\oxRegistry::get('mo_etracker__main')->takeEvents() as $event) {
            $calls[] = array_merge(['sendEvent', $event->getEventName()], $event->getParameters());
        }
        return json_encode($calls);
    }

}
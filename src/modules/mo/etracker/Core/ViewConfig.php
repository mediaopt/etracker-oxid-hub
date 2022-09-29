<?php

namespace Mediaopt\Etracker\Core;

use Mediaopt\Etracker\Main;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
 */


/**
 * This class provides functionality, which is accessed via the templates.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend oxViewConfig
 */
class ViewConfig extends ViewConfig_parent
{

    public $mo_etracker__checkOutViews = ['Basket', 'User', 'Payment', 'Order', 'Thankyou'];
    protected $mo_etracker__view;
    protected $mo_etracker__order;
    protected $mo_etracker__basket;
    protected $mo_etracker__root;
    protected $mo_etracker__config;
    protected $mo_etracker__vars = null;
    const MODULENAME = 'mo_etracker';
    const SHOPSYSTEMPREFIX = 'OXID_';

    /**
     * get etracker vars
     *
     */
    public function mo_etracker__getVars()
    {
        if ($this->mo_etracker__vars !== null) {
            return $this->mo_etracker__vars;
        }

        $main = Registry::get(Main::class);
        $this->mo_etracker__view = Registry::get(Config::class)->getActiveView();
        $this->mo_etracker__root = Registry::getConfig()->getConfigParam('mo_etracker__root');
        $etrackerVars = array();
        $etrackerVars['et_pagename'] = $this->mo_etracker__getPageName();
        $etrackerVars['et_areas'] = $this->mo_etracker__getAreas();
        $etrackerVars['et_se'] = Registry::getConfig()->getConfigParam('mo_etracker__sechannel');
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
    public function mo_etracker__getConfiguration(): array
    {
        $etrackerConfig = [];
        foreach (['securecode', 'securekey', 'sechannel', 'root', 'debug'] as $configurationKey) {
            $etrackerConfig['mo_etracker__' . $configurationKey] = Registry::getConfig()->getConfigParam('mo_etracker__' . $configurationKey);
        }
        return $etrackerConfig;
    }

    /**
     * get noscript vars
     *
     * @param $data
     */
    public function mo_etracker__getNoScriptVars($data)
    {
        return Registry::get(Main::class)->serializeData($data);
    }

    /**
     * get pagename
     *
     * @return string
     */
    protected function mo_etracker__getPageName(): string
    {
        //special case for index
        if ($this->getActiveClassName() === 'start') {
            return '__INDEX__';
        }

        $main = Registry::get(Main::class);
        $uri = $main->processShopUrl(Registry::get(Config::class)->getConfigParam('sShopURL')) ?: $_SERVER['REQUEST_URI'];
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

        $main = Registry::get(Main::class);
        if (preg_match('#/([^/]+)?(/|\\.html.*)$#', $uri, $matches)) {
            //remove special characters
            return $main->escapeCharacters($matches[1]);
        }

        $baseview = Registry::get(\OxidEsales\Eshop\Core\Request::class)->getRequestParameter('cl');
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
    protected function mo_etracker__getAreas(): string
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
            $areas .= Registry::get(Main::class)->translate($this->getActiveClassName());
            $areas = rtrim($areas, '/') . ',';

            //add root as prefix (//)
            $areas .= $this->mo_etracker__getRoot(false);
        }

        $areas .= $this->mo_etracker__getViewInformation();

        return $areas;
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
     * build string containing basketcontent
     *
     * @return string
     */
    protected function mo_etracker__getBasket(): string
    {
        return Registry::get(Main::class)->buildBasketString($this->basket);
    }

    /**
     * check if all necessary config-parameters are set
     *
     * @return bool
     */
    public function mo_etracker__isConfigComplete(): bool
    {
        return (bool)Registry::getConfig()->getConfigParam('mo_etracker__securecode');
    }

    /**
     * get path
     *
     * @return string
     */
    protected function mo_etracker__getPath(): string
    {
        $main = Registry::get(Main::class);
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
    protected function mo_etracker__processPaths(): string
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
    protected function mo_etracker__processTreePath(): string
    {
        $path = '';
        foreach ($this->mo_etracker__view->getTreePath() as $category) {
            $path .= Registry::get(Main::class)->getCategoryEntry($category, $this->mo_etracker__view);
        }
        return $path;
    }

    /**
     * process cattreepath
     *
     * @return string
     */
    protected function mo_etracker__processCatTreePath(): string
    {
        $tree = $this->mo_etracker__view->getCatTreePath();
        if (!$tree) {
            return '';
        }
        $path = '';
        foreach ($tree as $category) {
            $path .= Registry::get(Main::class)->getCategoryEntry($category, $this->mo_etracker__view);
        }
        return $path;
    }

    /**
     * get root
     *
     * @param bool $asNode
     * @return string
     */
    protected function mo_etracker__getRoot(bool $asNode = true): string
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

    /**
     * @param $asNode
     * @return string
     */
    protected function mo_etracker__addLanguagPrefix($asNode): string
    {
        $prefix = $this->mo_etracker__getLanguageAbbr() . '/';
        if (!$asNode) {
            $prefix .= '/';
        }

        return $prefix;
    }

    /**
     * @return int|string
     */
    public function mo_etracker__getLanguageAbbr(): int|string
    {
        $lang = Registry::get(Config::class)->getActiveShop()->getLanguage();
        $oxLang = Registry::get(Language::class);
        return $oxLang->getLanguageAbbr($lang);
    }


    /**
     * get active class and function
     *
     * @return string
     */
    protected function mo_etracker__getViewInformation(): string
    {
        $main = Registry::get(Main::class);
        $output = '';

        //class-name & function
        $output .= $main->translate($this->getActiveClassName());

        //functions
        if ($fnc = Registry::getRequest()->getRequestParameter('fnc')) {
            $output .= '/' . $main->translate($fnc);
        }
        return $output;
    }

    /**
     * checks whether current call has led to a 404 page
     */
    protected function mo_etracker__is404Call(): bool
    {
        $myConfig = Registry::get(Config::class);
        return get_class($myConfig->getActiveView()) == 'oxUBase';
    }

    /**
     * Transforms the queued events into events that can be transmitted to etracker.
     *
     * @return array
     */
    public function mo_etracker__getEventCalls(): array
    {
        $calls = [];
        foreach (Registry::get(Main::class)->takeEvents() as $event) {
            $calls[] = array_merge([$event->getEventName()], $event->getParameters());
        }
        return $calls;
    }

    /**
     * returns the oxid-version of the etracker module OXID_version
     *
     * @return string
     */
    public function mo_etracker__getModuleVersion(): string
    {
        $module = oxNew(Module::class);
        if (!$module->load(self::MODULENAME)) {
            return '';
        }
        return self::SHOPSYSTEMPREFIX . $module->getInfo('version');
    }

}

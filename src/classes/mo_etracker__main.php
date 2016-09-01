<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * Class containing the main functionality of the etracker module.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 */
class mo_etracker__main
{

    /**
     * Name that is used to store the event queue in the session.
     */
    const EVENT_QUEUE = 'mo_etracker__eventQueue';

    /**
     * True iff events were queued.
     *
     * @var bool
     */
    protected $eventsQueued = false;

    /**
     * prepend path
     *
     * @param string pagename
     * @param string path
     * @return string
     */
    public function prependPath($pagename, $path)
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
    public function processThankYouData($data)
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
    public function processShopUrl($shopUrl)
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
    public function getCategoryEntry($category, $view)
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
        return $this->escapeCharacters($subject) . '/';
    }

    /**
     * escape slashes for etracker
     *
     * @param string $string
     */
    public function escapeCharacters($string)
    {
        return str_replace(['/', '.', ','], '', $string);
    }

    /**
     * escape etracker vars
     * @param array $etrackerVars
     *
     * @return string
     */
    public function escapeValues($etrackerVars)
    {
        $isAlreadyEscaped = ['et_basket'];
        foreach ($etrackerVars as $key => $value) {
            if (in_array($key, $isAlreadyEscaped)) {
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
    public function cleanBasketString($string)
    {
        return rawurlencode(str_replace([',', ';'], '.', $string));
    }

    /**
     * serialize tracking params
     * @param string $string
     * @return string
     */
    public function serializeData($data)
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
    public function translate($identifier)
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
    public function buildBasketString($basket)
    {
        $etBasket = '';
        foreach ($basket->getContents() as $basketItem) {
            $product = $basketItem->getArticle();
            $etBasket .= $this->cleanBasketString($product->oxarticles__oxid->value) . ',';
            $etBasket .= $this->cleanBasketString($product->oxarticles__oxtitle->value) . ',';
            $etBasket .= $this->cleanBasketString($product->getCategory()->oxcategories__oxtitle->value) . ',';
            $etBasket .= $this->cleanBasketString($basketItem->getAmount()) . ',';
            $etBasket .= number_format($basketItem->getPrice()->getNettoPrice(), 2);
            $etBasket .= ';';
        }
        return $etBasket;
    }

    /**
     * Returns the triggered events and removes them.
     *
     * @see mo_etracker__main::$events
     * @return mo_etracker__event[]
     */
    public function takeEvents()
    {
        $eventQueue = \oxRegistry::getSession()->getVariable(self::EVENT_QUEUE);
        \oxRegistry::getSession()->setVariable(self::EVENT_QUEUE, []);
        if(!empty($eventQueue)) {
            $this->eventsQueued = true;
        }
        return is_array($eventQueue) ? $eventQueue : [];
    }

    /**
     * Returns true iff events have been queued.
     *
     * If there are events in the queue, we return true.
     * If there are no events in the queue because takeEvents was called, we return return.
     * Otherwise, we return false.
     *
     * @return bool
     */
    public function hasEvents()
    {
        $this->eventsQueued |= !empty(\oxRegistry::getSession()->getVariable(self::EVENT_QUEUE));
        return $this->eventsQueued;
    }

    /**
     * @param mo_etracker__event $event
     * @return $this
     */
    public function trigger(mo_etracker__event $event)
    {
        $eventQueue = \oxRegistry::getSession()->getVariable(self::EVENT_QUEUE);
        $updatedEventQueue = is_array($eventQueue) ? array_merge($eventQueue, [$event]) : [$event];
        \oxRegistry::getSession()->setVariable(self::EVENT_QUEUE, $updatedEventQueue);
        return $this;
    }

}
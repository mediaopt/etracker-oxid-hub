<?php

namespace Mediaopt\Etracker;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Core\Registry;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
 */

/**
 * Class containing the main functionality of the etracker module.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 */
class Main
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
     * @param string $pagename
     * @param string $path
     * @return string
     */
    public function prependPath(string $pagename, string $path): string
    {
        if (!preg_match('#(/|_|^)' . preg_quote($pagename) . '/*$#', $path)) {
            $path .= $pagename;
        }
        return $path;
    }

    /**
     * process shop url
     *
     * @param string $shopUrl
     * @return string
     */
    public function processShopUrl(string $shopUrl): string
    {
        //cut off prefixes if they are already in shop url (http://myurl.com/shop
        if (preg_match('#^http://[^/]+/(.+)$#', $shopUrl, $matches)) {
            return preg_replace('#' . preg_quote($matches[1]) . '$#', '', $shopUrl);
        }
        return $shopUrl;
    }


    /**
     * get category entry
     *
     * @param Category $category
     * @param mixed $view
     * @return string
     */
    public function getCategoryEntry($category, $view): string
    {
        $prefixClasses = ['aList', 'VendorList', 'Tag', 'RecommList', 'ManufacturerList', 'Details'];
        $subject = $category->oxcategories__oxtitle->value;
        foreach ($prefixClasses as $className) {
            if (is_a($view, $className)) {
                $translation = Registry::getLang()->translateString('MOET_PREFIX_' . strtoupper($view->getClassName()));
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
     * @return string
     */
    public function escapeCharacters(string $string): string
    {
        return str_replace(['/', '.', ','], '', $string);
    }

    /**
     * escape etracker vars
     *
     * @param array $etrackerVars
     * @return array
     */
    public function escapeValues(array $etrackerVars): array
    {
        return array_map('rawurlencode', $etrackerVars);
    }

    /**
     * serialize tracking params
     *
     * @param $data
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
    public function translate(string $identifier): string
    {
        $translationKey = 'MOET_' . strtoupper($identifier);
        $translation = Registry::getLang()->translateString($translationKey);
        return $translation !== $translationKey ? $translation : $identifier;
    }

    /**
     * Returns the triggered events and removes them.
     *
     * @return Event|array
     */
    public function takeEvents()
    {
        $eventQueue = Registry::getSession()->getVariable(self::EVENT_QUEUE);
        Registry::getSession()->setVariable(self::EVENT_QUEUE, []);
        if (!empty($eventQueue)) {
            $this->eventsQueued = true;
        }
        return is_array($eventQueue) ? $eventQueue : [];
    }

    /**
     * @param Event $event
     * @return $this
     */
    public function trigger(Event $event)
    {
        $eventQueue = Registry::getSession()->getVariable(self::EVENT_QUEUE);
        $updatedEventQueue = is_array($eventQueue) ? array_merge($eventQueue, [$event]) : [$event];
        Registry::getSession()->setVariable(self::EVENT_QUEUE, $updatedEventQueue);
        return $this;
    }
}

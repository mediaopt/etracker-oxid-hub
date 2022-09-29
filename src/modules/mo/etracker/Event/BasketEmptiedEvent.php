<?php

namespace Mediaopt\Etracker\Event;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
 */

/**
 * This event is issued if an item is removed from the basket.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 */
class BasketEmptiedEvent extends \Mediaopt\Etracker\Event\BasketEvent implements \Mediaopt\Etracker\Event
{

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return 'removeFromBasket';
    }
}

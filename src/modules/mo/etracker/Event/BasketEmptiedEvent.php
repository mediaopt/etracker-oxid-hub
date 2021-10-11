<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This event is issued if an item is removed from the basket.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 */
class basketEmptiedEvent extends basketEvent implements event
{

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'removeFromBasket';
    }

}
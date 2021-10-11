<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This event is issued if an item has been added to the noticelist.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class noticeListFilledEvent extends basketEvent implements event
{

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'insertToWatchlist';
    }

}
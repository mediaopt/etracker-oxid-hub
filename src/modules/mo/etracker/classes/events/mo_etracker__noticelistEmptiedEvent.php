<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This event is issued if an item is removed from the noticelist.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 */
class mo_etracker__noticelistEmptiedEvent extends mo_etracker__basketEvent implements mo_etracker__event
{

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'removeFromWatchlist';
    }

}
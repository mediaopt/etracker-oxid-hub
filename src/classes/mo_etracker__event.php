<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 */

/**
 * This interface has to be implemented by each event that is to be to transmitted to etracker.
 *
 * @author Andre Moelle <andre.moelle@mediaopt.de>
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 */
interface mo_etracker__event
{

    /**
     * @return string
     */
    public function getEventName();

    /**
     * @return array
     */
    public function getParameters();

}
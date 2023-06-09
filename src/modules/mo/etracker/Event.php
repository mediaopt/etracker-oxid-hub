<?php

namespace Mediaopt\Etracker;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 */

/**
 * This interface has to be implemented by each event that is to be to transmitted to etracker.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 */
interface Event
{
    /**
     * @return string
     */
    public function getEventName(): string;

    /**
     * @return array
     */
    public function getParameters(): array;
}

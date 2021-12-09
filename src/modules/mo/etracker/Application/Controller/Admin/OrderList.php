<?php

namespace Mediaopt\Etracker\Application\Controller\Admin;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This controller allows to cancel an order.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend
 */
class OrderList extends OrderList_parent
{

    /**
     * @extend
     * @return mixed
     */
    public function render()
    {
        $this->addTplParam('mo_etracker__include', true);
        return parent::render();
    }

}

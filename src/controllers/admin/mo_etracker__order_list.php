<?php
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
class mo_etracker__order_list extends mo_etracker__order_list_parent
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
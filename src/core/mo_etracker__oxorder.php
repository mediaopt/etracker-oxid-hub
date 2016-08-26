<?php

/**
 * TODO
 *
 * @author derksen mediaopt GmbH
 * @extend oxorder
 */
class mo_etracker__oxorder extends mo_etracker__oxorder_parent
{

    /**
     * TODO
     *
     * @extend cancelOrder
     * @return null
     */
    public function cancelOrder()
    {
        parent::cancelOrder();

        if (oxRegistry::getConfig()->getConfigParam('moetsecurekey')) {
            $this->mo_etracker__submitStorno();
        }
    }

    /**
     * @return null
     */
    protected function mo_etracker__submitStorno()
    {
        $response = $this->mo_etracker__submitStornoRequest($this->mo_etracker__buildQueryParameters());
        if ($response !== 'OK') {
            $intro = oxRegistry::getLang()->translateString('MOET_ERROR_STORNO_INTRO');
            $message = oxRegistry::getLang()->translateString('MOET_ERROR_STORNO_ID' . $response);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($intro . ' ' . $message);
        }
    }

    /**
     * @return array
     */
    protected function mo_etracker__buildQueryParameters()
    {
        return [
            'et' => oxRegistry::getConfig()->getConfigParam('moetsecurecode'),
            'et_key' => oxRegistry::getConfig()->getConfigParam('moetsecurekey'),
            'et_tonr' => $this->oxorder__oxordernr->value,
            'et_sale' => 2 // storno
        ];
    }

    /**
     * @param $query
     * @return string
     */
    protected function mo_etracker__submitStornoRequest($query)
    {
        return file_get_contents('http://www.etracker.de/lsconfirm.php?' . http_build_query($query));
    }

}
<?php

/**
 * TODO
 *
 * @author derksen mediaopt GmbH
 * @link https://www.mediaopt.de
 * @copyright (C) 2016 derksen mediaopt GmbH
 * @extend oxAdminView
 */
class mo_etracker_admin extends oxAdminView
{
    /**
     * @var string
     */
    protected $_sThisTemplate = 'mo_etracker_main.tpl';

    /**
     * @extend render
     * @return string
     */
    public function render()
    {
        $this->_aViewData['mo_etracker__config'] = $this->getConfig()->getShopConfVar('mo_etracker__config');
        return parent::render();
    }

    /**
     * @extend save
     * @return void
     */
    public function save()
    {
        $oxConfig = $this->getConfig();
        $etrackerConfig = $oxConfig->getParameter('mo_etracker__config');
        $oxConfig->saveShopConfVar('arr', 'mo_etracker__config', $etrackerConfig);
    }
}

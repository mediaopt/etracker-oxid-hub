<?php

/**
 *
 * @author derksen mediaopt GmbH
 */
class mo_etracker__category extends \oxAdminDetails
{

    /**
     * @extend
     * @return string
     */
    public function render()
    {
        parent::render();
        return 'mo_etracker__category.tpl';
    }

    /**
     * @extend save
     * @return mixed
     */
    public function save()
    {
        $category = \oxNew('oxcategory');
        $category->load($this->getEditObjectId());
        $category->assign($this->getConfig()->getRequestParameter('editval'));
        $category->save();
    }

    /**
     * Returns an object and loads it with the id returned by getEditObjectId.
     *
     * @return \oxCategory
     */
    public function mo_etracker__getEditObject()
    {
        $category = \oxNew('oxcategory');
        $id = $this->getEditObjectId();
        if (!empty($id) && $id != '-1') {
            $category->load($id);
        }
        return $category;
    }
}
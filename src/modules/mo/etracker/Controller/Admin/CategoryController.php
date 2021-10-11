<?php

namespace Mediaopt\Etracker\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Core\Registry;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This controller enables to set a category name specifically to be used with etracker.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 */
class CategoryController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{

    /**
     * @extend
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->addTplParam('mo_etracker__include', true);
        return 'mo_etracker__category.tpl';
    }

    /**
     * @extend save
     * @return mixed
     */
    public function save()
    {
        $category = oxNew(Category::class);
        $category->load($this->getEditObjectId());
        $category->assign(Registry::getRequest()->getRequestParameter('editval'));
        $category->save();
    }

    /**
     * Returns an object and loads it with the id returned by getEditObjectId.
     *
     * @return Category
     */
    public function mo_etracker__getEditObject()
    {
        $category = oxNew(Category::class);
        $id = $this->getEditObjectId();
        if (!empty($id) && $id != '-1') {
            $category->load($id);
        }
        return $category;
    }
}

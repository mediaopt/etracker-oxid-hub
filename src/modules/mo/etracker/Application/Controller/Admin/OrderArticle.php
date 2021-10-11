<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This controller enables to cancel a single article.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend Order_Article
 */
class orderArticle extends mo_etracker__order_article_parent
{

    /**
     * @extend
     * @return string
     */
    public function render()
    {
        $this->addTplParam('mo_etracker__include', true);
        return parent::render();
    }

    /**
     * @extend
     */
    public function storno()
    {
        $orderArticle = \oxNew('oxorderarticle');
        $orderArticle->load(\oxRegistry::getConfig()->getRequestParameter('sArtID'));
        if ($orderArticle->oxorderarticles__oxstorno->value != 1) {
            $order = \oxNew('oxOrder');
            $order->load($orderArticle->oxorderarticles__oxorderid->value);
            $event = \oxNew('orderPartiallyCanceledEvent', $order, $orderArticle);
        }

        parent::storno();

        if (isset($event)) {
            \oxRegistry::get('main')->trigger($event);
        }
    }
}
<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This controller enables to cancel a single article.
 *
 * @author Andre Moelle <andre.moelle@mediaopt.de>
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker
 * @extend Order_Article
 */
class mo_etracker__order_article extends mo_etracker__order_article_parent
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
        $alreadyCanceled = $orderArticle->oxorderarticles__oxstorno->value == 1;
        if (!$alreadyCanceled) {
            $order = \oxNew('oxOrder');
            $order->load($orderArticle->oxorderarticles__oxorderid->value);
            $event = new mo_etracker__orderPartiallyCanceledEvent($order, $orderArticle);
        }

        parent::storno();

        if (!$alreadyCanceled) {
            \oxRegistry::get('mo_etracker__main')->trigger($event);
        }
    }
}
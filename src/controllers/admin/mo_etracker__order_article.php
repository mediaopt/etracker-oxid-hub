<?php

/**
 * @author derksen mediaopt GmbH
 */
class mo_etracker__order_article extends mo_etracker__order_article_parent
{

    public function storno()
    {
        $orderArticle = \oxNew('oxorderarticle');
        $orderArticle->load(\oxRegistry::getConfig()->getRequestParameter('sArtID'));
        $alreadyCanceled = $orderArticle->oxorderarticles__oxstorno->value == 1;
        if(!$alreadyCanceled) {
            $order = \oxNew('oxOrder');
            $order->load($orderArticle->oxorderarticles__oxorderid->value);
            $event = new mo_etracker__orderPartiallyCanceledEvent($order, $orderArticle);
        }

        parent::storno();

        if (!$alreadyCanceled) {
            \oxRegistry::get('mo_etracker__helper')->trigger($event);
        }
    }
}
<?php

namespace Mediaopt\Etracker\Event;

use Mediaopt\Etracker\Converter;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This event contains functionality shared by basketEmptiedEvent and basketFilledEvent.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class BasketEvent
{

    /**
     * @see mo_etracker__converter::fromArticle
     */
    protected $product = null;

    /**
     * @var int
     */
    protected $quantity = 0;

    /**
     * Optional.
     *
     * @var string
     */
    protected $pageName = '';

    /**
     * mo_etracker__basketFilledEvent constructor.
     * @param Article $article
     * @param int $amount
     * @param string $pageName
     */
    public function __construct(Article $article, $amount, $pageName = '')
    {
        $this->product = Registry::get(Converter::class)->fromArticle($article);
        $this->quantity = $amount;
        $this->pageName = $pageName;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        $parameters = [$this->product, $this->quantity];
        if (!empty($this->pageName)) {
            $parameters[] = $this->pageName;
        }
        return $parameters;
    }
}

<?php

namespace Mediaopt\Etracker\Event;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;

/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 Mediaopt GmbH
 */


/**
 * This event is issued if a (potential) customers visits a product page.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class ProductViewedEvent implements \Mediaopt\Etracker\Event
{

    /**
     * @see mo_etracker__converter::fromArticle()
     * @var \stdClass
     */
    protected $product = null;

    /**
     * Optional.
     *
     * @var string
     */
    protected string $basketId = '';

    /**
     * Optional.
     *
     * @var string
     */
    protected string $pageName = '';

    /**
     * @param Article $article
     * @param string $basketId
     * @param string $pageName
     */
    public function __construct(Article $article, string $basketId = '', string $pageName = '')
    {
        $this->product = Registry::get(\Mediaopt\Etracker\Converter::class)->fromArticle($article);
        $this->basketId = $basketId;
        $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return 'viewProduct';
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        $parameters = [$this->product];
        foreach (['basketId', 'pageName'] as $property) {
            if (empty($this->{$property})) {
                break;
            }
            $parameters[] = $this->{$property};
        }
        return $parameters;
    }
}

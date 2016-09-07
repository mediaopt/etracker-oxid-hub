<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */

/**
 * This event contains functionality shared by basketEmptiedEvent and basketFilledEvent.
 *
 * @author Andre Moelle <andre.moelle@mediaopt.de>
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class mo_etracker__basketEvent
{

    /**
     * @see mo_etracker__converter::fromArticle
     * @var stdClass
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
    protected $basketId = '';

    /**
     * Optional.
     *
     * @var string
     */
    protected $pageName = '';

    /**
     * mo_etracker__basketFilledEvent constructor.
     * @param oxArticle $article
     * @param int $amount
     * @param string $basketId
     * @param string $pageName
     */
    public function __construct(\oxArticle $article, $amount, $basketId = '', $pageName = '')
    {
        $this->product = \oxRegistry::get('mo_etracker__converter')->fromArticle($article);
        $this->quantity = $amount;
        $this->basketId = $basketId;
        $this->pageName = $pageName;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $parameters = [$this->product, $this->quantity];
        foreach (['basketId', 'pageName'] as $property) {
            if (empty($this->$property)) {
                break;
            }
            $parameters[] = $this->$property;
        }
        return $parameters;
    }

}
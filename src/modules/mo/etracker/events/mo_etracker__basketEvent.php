<?php
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
    protected $pageName = '';

    /**
     * mo_etracker__basketFilledEvent constructor.
     * @param oxArticle $article
     * @param int $amount
     * @param string $pageName
     */
    public function __construct(\oxArticle $article, $amount, $pageName = '')
    {
        $this->product = \oxRegistry::get('mo_etracker__converter')->fromArticle($article);
        $this->quantity = $amount;
        $this->pageName = $pageName;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $parameters = [$this->product, $this->quantity];
        if (!empty($this->pageName)) {
            $parameters[] = $this->pageName;
        }
        return $parameters;
    }

}
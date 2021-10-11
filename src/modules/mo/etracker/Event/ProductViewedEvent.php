<?php
/**
 * For the full copyright and license information, refer to the accompanying LICENSE file.
 *
 * @copyright 2016 derksen mediaopt GmbH
 */


/**
 * This event is issued if a (potential) customers visits a product page.
 *
 * @version ${VERSION}, ${REVISION}
 * @package Mediaopt\Etracker\Event
 */
class productViewedEvent implements event
{

    /**
     * @see converter::fromArticle()
     * @var stdClass
     */
    protected $product = null;

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
     * @param oxArticle $article
     * @param string $basketId
     * @param string $pageName
     */
    public function __construct(\oxArticle $article, $basketId = '', $pageName = '')
    {
        $this->product = \oxRegistry::get('converter')->fromArticle($article);
        $this->basketId = $basketId;
        $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return 'viewProduct';
    }

    /**
     * @return array
     *@see event::getParameters()
     */
    public function getParameters()
    {
        $parameters = [$this->product];
        foreach(['basketId', 'pageName'] as $property) {
            if(empty($this->$property)) {
                break;
            }
            $parameters[] = $this->$property;
        }
        return $parameters;
    }

}
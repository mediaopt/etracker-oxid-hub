<?php

/**
 *
 * @author derksen mediaopt GmbH
 */
class mo_etracker__productViewedEvent implements mo_etracker__event
{

    /**
     * @see mo_etracker__converter::fromProduct()
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
     */
    public function __construct(\oxArticle $article)
    {
        $this->product = \oxRegistry::get('mo_etracker__converter')->fromProduct($article);
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
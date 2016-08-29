<?php

class mo_etracker__basketEvent
{

    /**
     * @see mo_etracker__converter::fromProduct
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
     * @param $amount
     */
    public function __construct(\oxArticle $article, $amount)
    {
        $this->product = \oxRegistry::get('mo_etracker__converter')->fromProduct($article);
        $this->quantity = $amount;
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
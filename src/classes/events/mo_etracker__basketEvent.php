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
     * @param int $amount
     * @param string $basketId
     */
    public function __construct(\oxArticle $article, $amount, $basketId = '')
    {
        $this->product = \oxRegistry::get('mo_etracker__converter')->fromProduct($article);
        $this->quantity = $amount;
        $this->basketId = $basketId;
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
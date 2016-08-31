<?php
/**
 * $Id:$
 */

/**
 * main singleton wiring class
 */
class mo_etracker__main
{
    /**
     * @var self
     */
    static protected $instance = null;

    /**
     * @var mo_etracker__helper
     */
    protected $helper;

    /**
     * singleton accessor
     *
     * @return self
     */
    static public function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * mock instance for unit-tests
     *
     * @param mo_etracker__main $mock
     */
    static public function unitTestSetMockInstance(mo_etracker__main $mock)
    {
        self::$instance = $mock;
    }

    /**
     * remove mock instance
     */
    static public function unitTestRemoveMockInstance()
    {
        self::$instance = null;
    }

    /**
     * constructor
     */
    protected function __construct()
    {
    }

    /**
     * getter method for helper
     *
     * @return mo_etracker__helper
     */
    public function getHelper()
    {
        if (is_null($this->helper)) {
            $this->helper = new mo_etracker__helper();
        }
        return $this->helper;
    }

}
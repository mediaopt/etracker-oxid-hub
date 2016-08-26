<?php
/**
 * $Id:$
 */

/**
 * main singleton wiring class
 */
class mo_etracker__main
{
    static protected $instance = null;

    /**
     * singleton accessor
     *
     * @return type
     */
    static public function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new mo_etracker__main();
        }
        return self::$instance;
    }

    /**
     * mock instance for unit-tests
     *
     * @param type $mock
     */
    static public function unitTestSetMockInstance($mock)
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
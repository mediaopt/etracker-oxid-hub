<?php

spl_autoload_register(function($class) {
    $prefix = 'mo_etracker';
    $suffix = 'Event';
    if(strpos($class, $prefix) !== false && strpos($class, $suffix) !== false) {
        require_once OX_BASE_PATH . 'modules/mo/etracker/classes/events/' . $class . '.php';
    }
});
<?php

spl_autoload_register(function ($class) {
    if ($class === 'mo_etracker__event') {
        require_once OX_BASE_PATH . 'modules/mo/etracker/Event.php';
    }
});

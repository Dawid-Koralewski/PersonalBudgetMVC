<?php

namespace App\Controllers;

/**
 * Authenticated base controller
 * 
 * PHP version 8.2.4
 */

 abstract class Authenticated extends \Core\Controller
 {
    /**
     * Require the oser to be authenticated before giving access to all methods in the controller
     * 
     * @return void
     */

     protected function before()
     {
        $this->requireLogin();
     }
 }
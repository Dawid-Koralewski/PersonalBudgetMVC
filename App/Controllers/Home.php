<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Home controller
 * 
 * PHP version 8.2.4
 */

 class Home extends \Core\Controller
 {
   /**
    * Before filter
    *
    * @return void
    */

    protected function before()
    {
      //echo "(before)";
      //return false;
    }

    /**
     * After filter
     * 
     * @return void
     */

     protected function after()
     {
      //echo " (after)";
     }
   
    /**
     * Show the index page
     * 
     * @return void
     */

     public function indexAction()
     {
        View::renderTemplate('Home/index.html');
     }
 }
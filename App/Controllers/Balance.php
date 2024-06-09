<?php

namespace App\Controllers;

use \Core\View;

/**
 * Login controller
 * 
 * PHP version 8.2.4
 */

 class Balance extends \Core\Controller
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
     * Show the login page
     * 
     * @return void
     */

     public function showAction()
     {
        View::renderTemplate('Balance/show.html');
     }
 }
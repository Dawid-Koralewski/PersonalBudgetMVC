<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Signup controller
 * 
 * PHP version 8.2.4
 */

 class Signup extends \Core\Controller
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

     public function newAction()
     {  
        View::renderTemplate('Signup/new.html');
     }

     public function createAction()
     {
      $user = new User($_POST);

      if ($user->save())
      {
        $user->sendActivationEmail();

        $this->redirect('/signup/success');
      }
      else
      {
        View::renderTemplate('Signup/new.html', [
              'user' => $user
        ]);
      }
     }

     /**
      * Show the signup success page
      *
      * @return void
      */

      public function successAction()
      {
        View::renderTemplate('Signup/success.html');
      }

     /**
      * Activate a new account
      *
      * @return void
      */

      public function activateAction()
      {
        User::activate($this->route_params['token']);

        $this->redirect('/signup/activated');
      }

     /**
      * Show the activation success page
      *
      * @return void
      */

      public function activatedAction()
      {
        View::renderTemplate('Signup/activated.html');
      }
 }
<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

/**
 * Home controller
 * 
 * PHP version 8.2.4
 */

 class Profile extends Authenticated
 {
    /**
     * User object
     * @var User object
     */
    public $user;

   /**
    * Before filter
    *
    * @return void
    */

    protected function before()
    {
      Authenticated::requireLogin();
      $this->user = Auth::getUser();
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
     * Show the profile of logged in user
     * 
     * @return void
     */

     public function showAction()
     {
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user
        ]);
     }

    /**
     * Show the form for editing the profile
     * 
     * @return void
     */

     public function editAction()
     {
        View::renderTemplate('Profile/edit.html', [
            'user' => $this->user
        ]);
     }

    /**
     * Update the profile
     * 
     * @return void
     */

     public function updateAction()
     {
        if ($this->user->updateProfile($_POST))
        {
            Flash::addMessage('Changes saved');

            $this->redirect('/profile/show');
        }
        else
        {
            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user
            ]);
        }
     }
 }
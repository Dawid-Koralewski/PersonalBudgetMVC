<?php

namespace Core;

use \App\Auth;
use \App\Flash;

/**
 * Base controller
 * 
 * PHP version 8.2.4
 */

 abstract class Controller
 {
    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     * 
     * @param array $route_params Parameters from the route
     * 
     * @return void
     */

     public function __construct($route_params)
     {
        $this->route_params = $route_params;
     }

     public function __call($name, $args)
     {
        $method = $name .'Action';

        if (method_exists($this, $method))
        {
            if ($this->before() !== false)
            {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        }
        else
        {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
     }

     /**
      * Before filter - called before an action method.
      *
      * @return void
      */
      
      protected function before()
      {

      }

      /**
       * After filter - called after an action method.
       * 
       * @return void
       */

       protected function after()
       {
 
       }

       /**
        * Redurect to a different page
        * 
        * @param string $url The relative URL
        *
        * @return void
        */

        public function redirect($url)
        {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
            exit;
        }

        /**
         * Require the user to be logged in before giving access to the requested page.
         * Remember the requsted page for later, then redirect to the login page.
         * 
         * @return void
         */

         public function requireLogin()
         {
            if (! Auth::getUser())
            {
                Flash::addMessage('Please login to access that page', Flash::INFO);
                Auth::rememberRequestedPage();
                $this->redirect('/login');
            }
         }
}
<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balance;

/**
 * Login controller
 * 
 * PHP version 8.2.4
 */

 class BalanceManager extends Authenticated
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
     * Show the balance page
     * 
     * @return void
     */

     public function showBalanceAction()
     {

        if ($_POST != NULL)
        {
          $balance = new Balance($_POST);
        }
        else 
        {
          $balance = new Balance();
        }

         View::renderTemplate('/BalanceManager/showBalance.html', [
         'expenses' => $balance->expenses,
         'incomes' => $balance->incomes,
         'balanceFromDate' => $balance->balanceFromDate,
         'balanceUntilDate' => $balance->balanceUntilDate,
         'totalAmountOfExpenses' => $balance->totalAmountOfExpenses,
         'totalAmountOfIncomes' => $balance->totalAmountOfIncomes
       ]);
     }
 }
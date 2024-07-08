<?php

namespace App\Controllers;

use DateTime;

use \Core\View;
use \App\Models\Expense;
use \App\Flash;

/**
 * Login controller
 * 
 * PHP version 8.2.4
 */

 class ExpenseManager extends Authenticated
 {
   /**
    * Before filter
    *
    * @return void
    */

    protected function before()
    {
      //echo "(before)";
      Authenticated::requireLogin();
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
     * Show the add expense page
     * 
     * @return void
     */

     public function showAction()
     {
        View::renderTemplate('ExpenseManager/show.html');
     }

    public function addAction()
    {
      if ($_SESSION['user_id'] == 0 || $_SESSION['user_id'] == NULL)
      {
        Flash::addMessage("No user discovered. Please try again.If it doesn't help, please relogin. If after relogin it still displays this message, please contact technical support","warning");
      }

      $expenseCategories = Expense::getCategoriesForCurrentUser();
      $expensePaymentMethods = Expense::getPaymentMethodsForCurrentUser();

      View::renderTemplate('ExpenseManager/add.html', [
        'expenseCategories' => $expenseCategories,
        'expensePaymentMethods' => $expensePaymentMethods
      ]);
    }

    public function createAction()
    {
    $expense = new Expense($_POST);

    $expenseCategories = Expense::getCategoriesForCurrentUser();
    $expensePaymentMethods = Expense::getPaymentMethodsForCurrentUser();

     if ($expense->save())
     {
       Flash::addMessage("Expense successfully added!");
       $this->redirect('/ExpenseManager/add');
       var_dump("tu juz nie?");
       return 0;
     }
     else
     {
       View::renderTemplate('/ExpenseManager/add.html', [
         'expense' => $expense,
         'expenseCategories' => $expenseCategories,
         'expensePaymentMethods' => $expensePaymentMethods
       ]);
     }
    }
 }
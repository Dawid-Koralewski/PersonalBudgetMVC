<?php

namespace App\Controllers;

use DateTime;

use \Core\View;
use \App\Models\Income;
use \App\Flash;

/**
 * Login controller
 * 
 * PHP version 8.2.4
 */

 class IncomeManager extends Authenticated
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
     * Show the add income page
     * 
     * @return void
     */

     public function showAction()
     {
        $incomes = Income::getAllIncomes();
        View::renderTemplate('IncomeManager/show.html', [
          'incomes' => $incomes
        ]);
     }

    public function addAction()
    {
      if ($_SESSION['user_id'] == 0 || $_SESSION['user_id'] == NULL)
      {
        Flash::addMessage("No user discovered. Please try again.If it doesn't help, please relogin. If after relogin it still displays this message, please contact technical support","warning");
      }

      $incomeCategories = Income::getCategoriesForCurrentUser();

      View::renderTemplate('IncomeManager/add.html', [
        'incomeCategories' => $incomeCategories
      ]);
    }

    public function createAction()
    {
    $income = new Income($_POST);

    $incomeCategories = Income::getCategoriesForCurrentUser();

     if ($income->save())
     {
       Flash::addMessage("Income successfully added!");
       $this->redirect('/IncomeManager/add');
     }
     else
     {
       View::renderTemplate('/IncomeManager/add.html', [
         'income' => $income,
         'incomeCategories' => $incomeCategories
       ]);
     }
    }

    /**
     * Delete income
     * 
     * @return void
     */    

     public function deleteAction()
     {
       income::deleteincome($_POST['incomeID']);
       $this->redirect('/incomeManager/show');
     }
 
     /**
      * Edit income
      * 
      * @return void
      */    
 
      public function editAction()
      {
       $income = income::getincomeByID($_POST['incomeID']);
 
       $incomeCategories = income::getCategoriesForCurrentUser();
 
        View::renderTemplate('/IncomeManager/edit.html', [
          'income' => $income,
          'incomeCategories' => $incomeCategories
        ]);
      }
 
     /**
      * Update income
      * 
      * @return void
      */    
 
      public function updateAction()
      {
         $income = new income($_POST);
         if ($income->update())
           Flash::addMessage("income updated succesfully");
         else
           Flash::addMessage("Fault when trying to update income, please try again.");
 
         $this->redirect('/incomeManager/show');
       }
 }
<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expense;
use \App\Models\Income;
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
     * Expense categories assigned to current user
     * @var array of Expense objects
     */
    public $expenseCategories;

    /**
     * Income categories assigned to current user
     * @var array of Income objects
     */
    public $incomeCategories;

   /**
    * Before filter
    *
    * @return void
    */

    protected function before()
    {
      Authenticated::requireLogin();
      $this->user = Auth::getUser();
      $this->expenseCategories = Expense::getCategoriesForCurrentUser();
      $this->incomeCategories = Income::getCategoriesForCurrentUser();
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
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories
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

    /**
     * Add expense category assigned to user
     * 
     * @return void
     */

     public function addExpenseCategoryAction()
     {
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories,
            'addingExpenseCategory' => true
        ]);
     }

    /**
     * Save new expense category assigned to user
     * 
     * @return void
     */

     public function saveNewExpenseCategoryAction()
     {
        Expense::saveNewExpenseCategory($_POST['newExpenseCategoryName']);

        $this->expenseCategories = Expense::getCategoriesForCurrentUser();
        $this->incomeCategories = Income::getCategoriesForCurrentUser();
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories
        ]);
     }

    /**
     * Edit expense category assigned to user
     * 
     * @return void
     */

     public function editExpenseCategoryAction()
     {
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories,
            'expenseCategoryToEditID' => $_POST['expenseCategoryID']
        ]);
     }

    /**
     * Update expense category assigned to user
     * 
     * @return void
     */

     public function updateExpenseCategoryAction()
     {
        if (Expense::updateExpenseCategoryName($_POST['expenseCategoryID'], $_POST['expenseCategoryName']))
            Flash::addMessage('Category name changed successfully');
        else
            Flash::addMessage('Couldn\'t changed name of category, please try again.', 'warning');

        $this->expenseCategories = Expense::getCategoriesForCurrentUser();
        $this->incomeCategories = Income::getCategoriesForCurrentUser();
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories
        ]);
     }

    /**
     * Delete expense caterogy assigned to user
     * 
     * @return void
     */

     public function deleteExpenseCategoryAction()
     {
        Expense::deleteExpenseCategory($_POST['expenseCategoryID']);

        $this->expenseCategories = Expense::getCategoriesForCurrentUser();
        $this->incomeCategories = Income::getCategoriesForCurrentUser();
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories
        ]);
     }

    /**
     * Add income category assigned to user
     * 
     * @return void
     */

     public function addIncomeCategoryAction()
     {
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories,
            'addingIncomeCategory' => true
        ]);
     }

    /**
     * Save new income category assigned to user
     * 
     * @return void
     */

     public function saveNewIncomeCategoryAction()
     {
        Income::saveNewIncomeCategory($_POST['newIncomeCategoryName']);

        $this->expenseCategories = Expense::getCategoriesForCurrentUser();
        $this->incomeCategories = Income::getCategoriesForCurrentUser();
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories
        ]);
     }

    /**
     * Edit income caterogy assigned to user
     * 
     * @return void
     */

     public function editIncomeCategoryAction()
     {
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories,
            'incomeCategoryToEditID' => $_POST['incomeCategoryID']
        ]);
     }

    /**
     * Update income category assigned to user
     * 
     * @return void
     */

     public function updateIncomeCategoryAction()
     {
        if (Income::updateIncomeCategoryName($_POST['incomeCategoryID'], $_POST['incomeCategoryName']))
            Flash::addMessage('Category name changed successfully');
        else
            Flash::addMessage('Couldn\'t changed name of category, please try again.', 'warning');

        $this->expenseCategories = Expense::getCategoriesForCurrentUser();
        $this->incomeCategories = Income::getCategoriesForCurrentUser();
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories
        ]);
     }

    /**
     * Delete income caterogy assigned to user
     * 
     * @return void
     */

     public function deleteIncomeCategoryAction()
     {
        Income::deleteIncomeCategory($_POST['incomeCategoryID']);

        $this->expenseCategories = Expense::getCategoriesForCurrentUser();
        $this->incomeCategories = Income::getCategoriesForCurrentUser();
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,
            'expenseCategories' => $this->expenseCategories,
            'incomeCategories' => $this->incomeCategories
        ]);
     }
 }
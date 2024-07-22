<?php

namespace App\Models;

use \Core\View;
use PDO;
use \App\Flash;

use \App\Models\Expense;

/**
 * Expense model
 * 
 * PHP version 8.2.4
 */

 class Balance extends \Core\Model
 {
    /**
     * User ID which balance should be displayed for
     * @var array 
     */
    public $user_id;

    /**
     * Expense categories
     * @var array 
     */
    public $expenses;

    /**
     * Income categories
     * @var array 
     */
    public $incomes;

    /**
     * Total value of expenses
     * @var double
     */
    public $totalAmountOfExpenses;

    /**
     * Total value of incomes
     * @var double
     */
    public $totalAmountOfIncomes;

    /**
     * Date until which balance data is pulled from database
     * @var date
     */
    public $balanceUntilDate;

    /**
     * Date from which balance data is pulled from database
     * @var date
     */
    public $balanceFromDate;

    /**
     * Error messages
     * 
     * @var array
     */

     public $errors = [];

      
    /** 
     * Class contructor
     * 
     * @param array $data Initial property values
     * 
     * @return void
     */

     public function __construct($data = [])
     {
        foreach ($data as $key => $value)
        {
            $this->$key = $value;
        }
        $this->user_id = $_SESSION['user_id'];

        if ($this->balanceUntilDate == NULL)
          $this->balanceUntilDate = date('Y-m-d', time());

        if ($this->balanceFromDate == NULL)
          $this->balanceFromDate = date('Y-m-01', time());
        
        $this->expenses = $this->getExpensesForCurrentUser();
        $this->incomes = $this->getIncomesForCurrentUser();
        $this->totalAmountOfExpenses = $this->getTotalAmountOfExpensesForCurrentUser();
        $this->totalAmountOfIncomes = $this->getTotalAmountOfIncomesForCurrentUser();
     }

    /**
     * Get expenses for current user
     * 
     * @param
     * 
     * @return double Expenses grouped by categories
     */

     private function getExpensesForCurrentUser()
      {
        $sql = 'SELECT name, SUM(amount) as sum FROM expenses_category_assigned_to_users, expenses WHERE expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id AND expenses.user_id = :user_id AND :balanceFromDate <= expenses.date_of_expense AND expenses.date_of_expense <= :balanceUntilDate GROUP BY expense_category_assigned_to_user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':balanceFromDate', $this->balanceFromDate, PDO::PARAM_STR);
        $stmt->bindValue(':balanceUntilDate', $this->balanceUntilDate, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_GROUP);

        $stmt->execute();

        return $stmt->fetchAll();
      }

    /**
     * Get total amount of expenses for current user
     * 
     * @param
     * 
     * @return double Total amount of expenses for current user
     */
    private function getTotalAmountOfExpensesForCurrentUser()
    {
        $sql = 'SELECT SUM(amount) as sum FROM expenses WHERE user_id = :user_id AND :balanceFromDate <= date_of_expense AND date_of_expense <= :balanceUntilDate';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':balanceFromDate', $this->balanceFromDate, PDO::PARAM_STR);
        $stmt->bindValue(':balanceUntilDate', $this->balanceUntilDate, PDO::PARAM_STR);

        $stmt->execute();
        
        $result = $stmt->fetch();

        return $result['sum'];
    }

    /**
     * Get incomes for current user
     * 
     * @param
     * 
     * @return double Incomes grouped by categories
     */

     private function getIncomesForCurrentUser()
      {
        $sql = 'SELECT name, SUM(amount) as sum FROM incomes_category_assigned_to_users, incomes WHERE income_category_assigned_to_user_id = incomes_category_assigned_to_users.id AND incomes.user_id = :user_id AND :balanceFromDate <= incomes.date_of_income AND incomes.date_of_income <= :balanceUntilDate GROUP BY income_category_assigned_to_user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':balanceFromDate', $this->balanceFromDate, PDO::PARAM_STR);
        $stmt->bindValue(':balanceUntilDate', $this->balanceUntilDate, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_GROUP);

        $stmt->execute();

        return $stmt->fetchAll();
      }

    /**
     * Get total amount of incomes for current user
     * 
     * @param
     * 
     * @return double Total amount of incomes for current user
     */
    private function getTotalAmountOfIncomesForCurrentUser()
    {
        $sql = 'SELECT SUM(amount) as sum FROM incomes WHERE user_id = :user_id AND :balanceFromDate <= date_of_income AND date_of_income <= :balanceUntilDate';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':balanceFromDate', $this->balanceFromDate, PDO::PARAM_STR);
        $stmt->bindValue(':balanceUntilDate', $this->balanceUntilDate, PDO::PARAM_STR);

        $stmt->execute();
        
        $result = $stmt->fetch();

        return $result['sum'];
    }
 }

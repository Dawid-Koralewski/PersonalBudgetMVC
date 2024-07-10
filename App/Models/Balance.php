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
     * Total amount of expenses
     * @var double
     */
    public $totalAmountOfExpenses;

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
        $this->expenses = $this->getExpensesForCurrentUser();
        $this->totalAmountOfExpenses = $this->getTotalAmountOfExpensesForCurrentUser();
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
        $sql = 'SELECT expense_category_assigned_to_user_id, SUM(amount) as sum FROM expenses WHERE user_id = :user_id GROUP BY expense_category_assigned_to_user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
 
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
        $sql = 'SELECT SUM(amount) as sum FROM expenses WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->execute();
        
        $result = $stmt->fetch();

        return $result['sum'];
    }
 }

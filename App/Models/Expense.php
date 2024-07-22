<?php

namespace App\Models;

use DateTime;

use \Core\View;
use PDO;
use \App\Flash;


/**
 * Expense model
 * 
 * PHP version 8.2.4
 */

 class Expense extends \Core\Model
 {
    /**
     * Expense ID
     * @var int 
     */
    public $id;

    /**
     * User ID, which expense is assigned to
     * @var int 
     */
    public $user_id;

    /**
     * Expense category ID
     * @var string
     */
    public $category;

    /**
     * Expense payment method
     * @var string
     */
    public $paymentMethod;

    /**
     * Expense amount
     * @var float
     */
    public $amount;

    /**
     * Expense date
     * @var string
     */
    public $date;

    /**
     * Expense date in DateTime format
     * @var DateTime
     */
    public $dateInDateTimeFormat;

    /**
     * Expense comment
     * @var string
     */
    public $comment;

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
     }

    /**
     * Get expense categories by userID
     * 
     * @param
     * 
     * @return mixed Expense category/ies array if found, false otherwise
     */

     public static function getCategoriesForCurrentUser()
     {
       $sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :user_id';

       $db = static::getDB();
       $stmt = $db->prepare($sql);
       $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

       $stmt->setFetchMode(PDO::FETCH_GROUP);

       $stmt->execute();

       return $stmt->fetchAll();
     }

    /**
     * Get expense payment methods by userID
     * 
     * @param
     * 
     * @return mixed Expense payment method/s array if found, false otherwise
     */

     public static function getPaymentMethodsForCurrentUser()
     {
       $sql = 'SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :user_id';

       $db = static::getDB();
       $stmt = $db->prepare($sql);
       $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

       $stmt->setFetchMode(PDO::FETCH_GROUP);

       $stmt->execute();

       return $stmt->fetchAll();
     }

    /** 
     * Save ther expense model with the current property values
     * 
     * @return true if execution was successful, false otherwise
     */

     public function save()
     {
        $this->validate();

        if (empty($this->errors))
        {
            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id	, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
                    VALUES (:user_id, :category, :paymentMethod, :amount, :date, :comment)';
            
            $expenseCategoryId = $this->getExpenseCategoryId();
            $paymentMethodId = $this->getPaymentMethodId();

            $db = static::getDB();
            
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':category', $expenseCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(':paymentMethod', $paymentMethodId, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
     }


    /**
      * Validate current property values, adding validation error messages to the errors array property
      *
      * @return void
      */

     public function validate()
     {
       // Category
       if ($this->category == '')
       {
           $this->errors[] = 'Category is required';
       }

       // Date
       if ($this->date == '')
       {
           $this->errors[] = 'Date is required';
       }

       $dateInDateTimeFormat = DateTime::createFromFormat('Y-m-d', $this->date);
       if (!checkdate($dateInDateTimeFormat->format('m'),$dateInDateTimeFormat->format('d'),$dateInDateTimeFormat->format('Y')))
       {
           $this->errors[] = 'Date is required';
       }
     }


     /**
      * Returns expense category ID
      *
      * @return int ID of expense category
      */

      private function getExpenseCategoryId()
      {
        $sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name = :category';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
 
        $stmt->execute();
 
        $result = $stmt->fetch();

        return $result['id'];
      }


    /**
      * Returns payment method ID
      *
      * @return int ID of expense category
      */

      private function getPaymentMethodId()
      {
        $sql = 'SELECT id FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name = :paymentMethod';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':paymentMethod', $this->paymentMethod, PDO::PARAM_STR);
 
        $stmt->execute();
 
        $result = $stmt->fetch();

        return $result['id'];
      }

    /**
      * Returns all expenses
      *
      * @return array expenses
      */

      public static function getAllExpenses()
      {
        $sql = 'SELECT expenses.id as id, name, amount, expense_comment FROM expenses_category_assigned_to_users, expenses WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id AND expenses.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
 
        $stmt->setFetchMode(PDO::FETCH_GROUP);

        $stmt->execute();

        return $stmt->fetchAll();
      }

    /**
      * Delete expense
      *
      * @return array expenses
      */

      public static function deleteExpense($id)
      {
        $sql = 'DELETE FROM expenses WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
 
        $stmt->setFetchMode(PDO::FETCH_GROUP);

        return $stmt->execute();
      }

    /**
      * Get expense by ID
      *
      * @return Expense
      */

      public static function getExpenseByID($id)
      {
        $sql = 'SELECT expenses_category_assigned_to_users.name as category_name, payment_methods_assigned_to_users.name as payment_method_name, expenses.id as id, amount, date_of_expense, expense_comment FROM expenses_category_assigned_to_users, payment_methods_assigned_to_users, expenses WHERE expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id AND payment_methods_assigned_to_users.id = expenses.payment_method_assigned_to_user_id AND expenses.id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetch();

        return $result;
      }

/** 
     * Update the expense with model with the current property values
     * 
     * @return true if execution was successful, false otherwise
     */

     public function update()
     {
        $this->validate();

        if (empty($this->errors))
        {
            $sql = 'UPDATE expenses
                    SET expense_category_assigned_to_user_id = :category, payment_method_assigned_to_user_id = :paymentMethod, amount = :amount, date_of_expense = :date, expense_comment = :comment
                    WHERE id = :id';
            
            $expenseCategoryId = $this->getExpenseCategoryId();
            $paymentMethodId = $this->getPaymentMethodId();

            $db = static::getDB();
            
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':category', $expenseCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(':paymentMethod', $paymentMethodId, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
     }
 }

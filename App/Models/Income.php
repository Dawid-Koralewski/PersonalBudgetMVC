<?php

namespace App\Models;

use DateTime;

use \Core\View;
use PDO;
use \App\Flash;


/**
 * Income model
 * 
 * PHP version 8.2.4
 */

 class Income extends \Core\Model
 {
    /**
     * Income ID
     * @var int 
     */
    public $id;

    /**
     * User ID, which income is assigned to
     * @var int 
     */
    public $user_id;

    /**
     * Income category ID
     * @var string
     */
    public $category;

    /**
     * Income amount
     * @var float
     */
    public $amount;

    /**
     * Income date
     * @var string
     */
    public $date;

    /**
     * Income date in DateTime format
     * @var DateTime
     */
    public $dateInDateTimeFormat;

    /**
     * Income comment
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
     * Get income categories by userID
     * 
     * @param
     * 
     * @return mixed income category/ies array if found, false otherwise
     */

     public static function getCategoriesForCurrentUser()
     {
       $sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :user_id';

       $db = static::getDB();
       $stmt = $db->prepare($sql);
       $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

       $stmt->setFetchMode(PDO::FETCH_GROUP);

       $stmt->execute();

       return $stmt->fetchAll();
     }

    /** 
     * Save ther income model with the current property values
     * 
     * @return true if execution was successful, false otherwise
     */

     public function save()
     {
        $this->validate();

        if (empty($this->errors))
        {
            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id	, amount, date_of_income, income_comment)
                    VALUES (:user_id, :category, :amount, :date, :comment)';
            
            $incomeCategoryId = $this->getIncomeCategoryId();

            $db = static::getDB();
            
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':category', $incomeCategoryId, PDO::PARAM_INT);
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
      * Returns income category ID
      *
      * @return int ID of income category
      */

      private function getIncomeCategoryId()
      {
        $sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :category';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
 
        $stmt->execute();
 
        $result = $stmt->fetch();

        return $result['id'];
      }

    /**
      * Returns all incomes
      *
      * @return array incomes
      */

      public static function getAllincomes()
      {
        $sql = 'SELECT incomes.id as id, name, amount, income_comment FROM incomes_category_assigned_to_users, incomes WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id AND incomes.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
 
        $stmt->setFetchMode(PDO::FETCH_GROUP);

        $stmt->execute();

        return $stmt->fetchAll();
      }

    /**
      * Delete income
      *
      * @return array incomes
      */

      public static function deleteincome($id)
      {
        $sql = 'DELETE FROM incomes WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
 
        $stmt->setFetchMode(PDO::FETCH_GROUP);

        return $stmt->execute();
      }

    /**
      * Get income by ID
      *
      * @return income
      */

      public static function getincomeByID($id)
      {
        $sql = 'SELECT incomes_category_assigned_to_users.name as category_name, incomes.id as id, amount, date_of_income, income_comment FROM incomes_category_assigned_to_users, payment_methods_assigned_to_users, incomes WHERE incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id AND incomes.id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetch();

        return $result;
      }

/** 
     * Update the income with model with the current property values
     * 
     * @return true if execution was successful, false otherwise
     */

     public function update()
     {
        $this->validate();

        if (empty($this->errors))
        {
            $sql = 'UPDATE incomes
                    SET income_category_assigned_to_user_id = :category, amount = :amount, date_of_income = :date, income_comment = :comment
                    WHERE id = :id';
            
            $incomeCategoryId = $this->getincomeCategoryId();

            $db = static::getDB();
            
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':category', $incomeCategoryId, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
     }
 }

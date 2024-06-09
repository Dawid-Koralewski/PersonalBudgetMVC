<?php

namespace App\Models;

use \App\Token;
use \App\Mail;
use \Core\View;
use PDO;

/**
 * User model
 * 
 * PHP version 8.2.4
 */

 class User extends \Core\Model
 {
    /**
     * Username
     * @var int 
     */
    public $id;

    /**
     * Username
     * @var string
     */
    public $name;

    /**
     * User's email
     * @var 
     */
    public $email;

    /**
     * User's password
     * @var string
     */
    public $password;

    /**
     * User's password hash
     * @var string
     */
    public $password_hash;

    /**
     * User's password reset hash
     * @var string
     */
    public $password_reset_hash;
    
    /**
     * Date and time of expiration of password reset request
     * @var string
     */
    public $password_reset_expires_at;

    /**
     * Password reset token
     * @var string
     */
    public $password_reset_token;

    /**
     * Error messages
     * 
     * @var array
     */

     public $errors = [];

    /**
     * Token to be remembered
     * 
     * @var string
     */

     public $remember_token;

    /**
     * Cookie expiry timestamp
     * 
     * @var int
     */

     public $expiry_timestamp;

     /**
      * Account activation token
      * 
      * @var string
      */

     public $activation_token;

     /**
      * Account activation token hash
      * 
      * @var string
      */

      public $activation_hash;

     /**
      * Boolean defining if account is activated
      * 
      * @var boolean
      */

      public $is_active;
      
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
     }
    
    /** 
     * Save ther user model with the current property values
     * 
     * @return void
     */

     public function save()
     {
        $this->validate();

        if (empty($this->errors))
        {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();

            $sql = 'INSERT INTO users (name, email, password_hash, activation_hash)
                    VALUES (:name, :email, :password_hash, :activation_hash)';
            
            $db = static::getDB();
            
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

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
        // Name
        if ($this->name == '')
        {
            $this->errors[] = 'Name is required';
        }

        // emaill address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false)
        {
            $this->errors[] = 'Invalid email';
        }

        if (static::emailExists($this->email, $this->id ?? null))
        {
            $this->errors[] = 'email already taken';
        }

        //Password
        if (isset($this->password))
        {        
            if (strlen($this->password) < 6)
            {
                $this->errors[] = 'Please enter at least 6 characters for the password';
            }

            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0)
            {
                $this->errors[] = 'Password needs at least one letter';
            }

            if (preg_match('/.*\d+.*/i', $this->password) == 0)
            {
                $this->errors[] = 'Password needs at least one number';
            }
        }
      }

      /**
       * See if a user record already exists with the specified email
       * 
       * @param string $email email address to search for
       * 
       * @return boolean True if a record already exists with the specified email, false otherwise
       */

      public static function emailExists($email, $ignore_id = null)
      {
        $user = static::findByEmail($email);
        
        if ($user)
        {
            if ($user->id != $ignore_id)
            {
                return true;
            }

            return false;
        }
      }

      /**
       * Find a user model by email address
       * 
       * @param string $email email address to search for
       * 
       * @return mixed User object if found, false otherwise
       */

       public static function findByEmail($email)
       {
         $sql = 'SELECT * FROM users WHERE email = :email';
 
         $db = static::getDB();
         $stmt = $db->prepare($sql);
         $stmt->bindValue(':email', $email, PDO::PARAM_STR);
 
         $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

         $stmt->execute();
 
         return $stmt->fetch();
       }

       /**
        * Authenticate a user by email and password.
        * 
        * @param string $email email address
        * @param string $password password
        *
        * @return mixed The user object or false if authentication fails
        */

        public static function authenticate($email, $password)
        {
            $user = static::findByEmail($email);

            if ($user && $user->is_active)
            {
                if (password_verify($password, $user->password_hash))
                {
                    return $user;
                }
            }
            
            return false;
        }

        /**
         * Find a user model by ID
         * 
         * @param string $id The user ID
         * 
         * @return mixed User object if found, false otherwise
         */

         public static function findByID($id)
         {
            $sql = 'SELECT * FROM users WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    
            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
   
            $stmt->execute();
    
            return $stmt->fetch();
         }

         /**
          * Remember the login from inserting a new unique token into the remembered_logins table
          * for this user record
          *
          * @return boolean True if the login was remembered successfullt, false otherwise
          */

          public function rememberLogin()
          {
            $token = new Token();
            $hashed_token = $token->getHash();
            $this->remember_token = $token->getValue();

            $this->expiry_timestamp = time() + 60 * 60 * 24 * 30; // 30 days from now
            
            $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                    VALUES (:token_hash, :user_id, :expires_at)';
            
            $db = static::getDB();
            
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

            return $stmt->execute();
        }

        /**
         * Send password reset unstructions to the user specified
         * 
         * @param string $email The email address
         * 
         * @return void
         */

         public static function sendPasswordReset($email)
         {
            $user = static::findByEmail($email);

            if ($user)
            {
                if ($user->startPasswordReset())
                {
                    $user->sendPasswordResetEmail();
                }
            }
         }

         /**
          * Start the password reset process by generating a new token and expiry
          * 
          * @return void
          */

          protected function startPasswordReset()
          {
            $token = new Token();
            $hashed_token = $token->getHash();
            $this->password_reset_token = $token->getValue();

            $expiry_timestamp = time() + 60 * 60 * 2; // 2 hours from now

            $sql = 'UPDATE users
                    SET password_reset_hash = :token_hash,
                        password_reset_expires_at = :expires_at
                    WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
            $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
          }

          /** 
           * Send password reset instructions in an email to the user
           * 
           * @return void
           */

           protected function sendPasswordResetEmail()
           {
            $url= 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;

            $htmlEmailContent = View::getTemplate('Password/reset_email.html', ['url' => $url]);

            Mail::send($this->email, 'Password reset', $htmlEmailContent);
           }

          /** 
           * Find a user model by password reset token and expiry
           * 
           * @param string $token Password reset token sent to user
           * 
           * @return mixed User object if found and the token hasn't expired, null otherwise
           */

           public static function findByPasswordReset($token)
           {
            $token = new Token($token);
            $hashed_token = $token->getHash();
            
            $sql = 'SELECT * FROM users
                    WHERE password_reset_hash = :token_hash';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

            $stmt->execute();

            $user = $stmt->fetch();

            if ($user)
            {

                // Check password reset token hasn't expired
                if (strtotime($user->password_reset_expires_at) > time())
                {
                    return $user;
                }
            }
           }

           /**
            * Reset the password
            * 
            * @param string $password The new password
            *
            * @return boolean True if the password was updated successfully, false otherwise
            */
           
            public function resetPassword($password)
            {
                $this->password = $password;

                $this->validate();

                if (empty($this->errors))
                {
                    $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

                    $sql = 'UPDATE users
                            SET password_hash = :password_hash,
                                password_reset_hash = NULL,
                                password_reset_expires_at = NULL
                            WHERE id = :id';
                    
                    $db = static::getDB();
                    $stmt = $db->prepare($sql);

                    $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
                    $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

                    return $stmt->execute();
                }

                return false;
            }

          /** 
           * Send an activation link to the user
           * 
           * @return void
           */

           public function sendActivationEmail()
           {
            $url= 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

            $htmlEmailContent = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

            Mail::send($this->email, 'Account activation', $htmlEmailContent);
           }

          /** 
           * Activate user account with the specified activation token
           * 
           * @param string $value Activation token from the URL
           * 
           * @return void
           */

           public static function activate($value)
           {
            $token = new Token($value);
            $hashed_token = $token->getHash();

            $sql = 'UPDATE users
                    SET is_active = 1,
                        activation_hash = null
                    WHERE activation_hash = :hashed_token';

            $db = static::getDB();

            $stmt = $db->prepare($sql);

            $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

            $stmt->execute();
           }

          /** 
           * Update user's profile
           * 
           * @param array $data Data from the edit profile form
           * 
           * @return boolead True if data was updated, false otherwise
           */

           public function updateProfile($data)
           {
            $this->name = $data['name'];
            $this->email = $data['email'];

            // Only validate and update the password if a value was provided
            if ($data['password'] != '')
            {
                $this->password = $data['password'];
            }

            $this->validate();

            if (empty($this->errors))
            {
                $sql = 'UPDATE users
                        SET name = :name,
                            email = :email';

                if (isset($this->password))
                {
                    $sql .= ', password_hash = :password_hash';
                }
                
                $sql .= "\nWHERE id = :id";

                $db = static::getDB();
                $stmt = $db->prepare($sql);

                $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
                $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
                $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

                if (isset($this->password))
                {
                    $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                    $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
                }

                return $stmt->execute();
            }

            return false;

           }
 }

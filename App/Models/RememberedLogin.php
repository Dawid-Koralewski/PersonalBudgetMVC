<?php

namespace App\Models;

use \App\Token;
use PDO;

/**
 * Remembered login model
 * 
 * PHP version 8.2.4
 */

 class RememberedLogin extends \Core\Model
 {
    /**
     * Token hash of remembered login
     * @var int 
     */
    public $token_hash;

    /**
     * User ID of remembered login
     * @var string
     */
    public $user_id;

    /**
     * Expire cookie timestamp
     * @var 
     */
    public $expires_at;

    /**
     * Find a rememberred login model by the token
     * 
     * @param string $token The remembered login toke
     * 
     * @return mixed Remembered login object if found, false otherwise
     */

     public static function findByToken($token)
     {
        $token = new Token($token);
        $token_hash = $token->getHash();

        $sql = 'SELECT * FROM remembered_logins 
                WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
     }

     /**
      * Get the user model associated with this remembered login
      * 
      * @return User the user model
      */

      public function getUser()
      {
        return User::findByID($this->user_id);
      }

      /**
       * Check if the remember token has expired or not, based on the current system time
       * 
       * @return boolean True if the token has expired, false otherwise
       */

       public function hasExpired()
       {
        return (strtotime($this->expires_at) < time());
       }

       /**
        * Delete this model
        *
        * @return void
        */

        public function delete()
        {
        $sql = 'DELETE FROM remembered_logins 
                WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);

        $stmt->execute();
        }
 }
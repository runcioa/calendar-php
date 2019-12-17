<?php 

/**
 * class.db_connect.php
 */

declare(strict_types=1);

/**
 * Database actions (DB access, validation, ecc.)
 * 
 * PHP version 7
 * 
 * @author Antonio Runcio
 * @copyright
 * @license
 * 
 */

class DB_connect {

/**
 * Store a database object
 * 
 * @var object A database object
 */

 protected $db;

 /**
  *Check for DB object or vcreate one if one isn't found
  * 
  *@param object $db A database object
  */

  protected function __construct($db=NULL)
  {
    if(is_object($db))
    {
      $this->db=$db;
    }
    else
    {
      //Constant are defined in /sys/config/db-cred.inc.php
      $dns = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
      try 
      {
        $this->db = new PDO($dns, DB_USER, DB_PASS);
        echo 'connesso';
      } catch (Exception $e) 
      {
        //If the DB connection fails, output the error
        die($e->getMessage());
      }
    }
  }
  
}

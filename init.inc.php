<?php

/**
 * init.inc.php
 */

declare(strict_types=1);

/**
 * Including the necessary configuration info
 */

 include_once 'db-cred.inc.php';

 /**
  * Define constants for configuration info
  */

  foreach ($C as $name=>$val)
  {
    define($name, $val);
  }

  /**
   * Create PDO object
   */

   $dns = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
   echo $dns;
   $dbo = new PDO($dns, DB_USER, DB_PASS);

   /**
    * Define auto-load function for classes
    */

    function __autoload($class)
    {
      $filename = "class.". $class . ".inc.php";
      echo $filename;
      if (file_exists($filename))
      {
        include_once $filename;
      }
    }

    ?>
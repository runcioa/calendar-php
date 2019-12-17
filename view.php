<?php

declare(strict_types=1);

/**
 * Make sure the event id was passed
 */
if (isset($_GET['event_id']))
{
  /**
   * Make sure ID is an integer
   */

   $id = preg_replace('/[^0-9]/','',$_GET['event_id']);

   /**
    * If the Id isn't valid, send the user to the main page
    */

    if(empty($id))
    {
      header("Location: ./");
      exit;
    }
}
else
{

    /** 
     * Sernd the user to the main page if no ID is supplied
     */

    header("Location: ./");
    exit;

}

/**
 * Include necessary files
 */

 include_once 'init.inc.php';

 /**
  * Output the header
  */
  $page_title = "View event";
  $css_file = array("style.css");
  include_once 'header.inc.php';

  /**
   * Load the calendar
   */

   $cal = new Calendar($dbo);

   ?>

   <div id="content" >
   <?php echo $cal->displayEvent($id) ?>

   <a href="./">&laquo; Back to the calendar</a>
   </div>

   <?php 

   /**
    * Output the footer
    */

    include_once ('footer.inc.php');

    ?>

    
<?php

/**
 * class.event.inc.php
 */

declare(strict_types=1);

/**
 * Stores event information
 * 
 */

 class Event
 {
   /**
    * The event ID
    *@var int
    */

    public $id;

    /**
     * The event title
     * @var string
     */

     public $title;

     /**
      *The event description 
      *@var string
      */

      public $description;

      /**
       * The event start time
       * @var string
       */
      public $start;

      /**
       * The event end time
       * @var string
       */
      public $end;

      /**
       * Accept an array of event data and store it
       * 
       * @param array $event Associative array of event data
       * @return void
       */

       public function __construct($event)
       {
         if (is_array($event))
         {
           $this->id = $event['event_id'];
           $this->title = $event['event_title'];
           $this->description = $event['event_desc'];
           $this->start = $event['event_start'];
           $this->end = $event['event_end'];

         }
         else
         {
           throw new Exception("No event data was supplied");
         }
         
       }
    

 }
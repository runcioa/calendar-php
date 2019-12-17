<?php

/**
 * class.calendar.inc.php
 */

declare(strict_types=1);

/**
 * Build and manipulate an event calendar
 * 
 */

class Calendar extends DB_connect
{

  /**
   * The date from which the calendar should be build
   * 
   * Stored in YYY-MM-YY HH:MM:SS format
   * 
   * @var string the date to use for the calendar
   */

  private $_usedate;

  /**
   * The month for which the calendar is being build
   *
   *@var int the month being used 
   */

  private $_m;

  /**
   * The year from which the month's start day selected
   * 
   * @var the year being used 
   */

  private $_y;


  /**
   * The number of days in the month being used
   *
   *@var the int number of days in the month
   */

  private $_daysInMonth;

  /**
   * the index of the day of the week the month starts on (0-6)
   * 
   * @var int the day of the week the month start on
   */

  private $_startDay;


  /** 
   * Create database object and stoer relevant data
   *
   *Upon instantiation, this class accepts a database object 
   *that, if not null, is stored in the object's private $_db
   *property. If null, a new PDO objecct is created and stored
   *instead.
   *
   *Adding info is gathered and stored in this method,
   *including the month from which the calendar is to be built,
   *how many days are in said month, what day the month starts
   *on, and what day it is currently.
   *
   *@param object $dbo a database object
   *@param string  $useDate the date to use to build the calendar
   *@return void
   */

  public function __construct($dbo = NULL, $useDate = NULL)
  {
    /**
     * Call the parent constructor to check for
     * a database object
     */

    parent::__construct($dbo);

    /**
     * Gather and store data relevant to the month
     */

    if (isset($useDate)) {
      $this->_usedate = $useDate;
    } else {
      $this->_usedate = date('Y-m-d H:i:s');
    }

    /**
     * Convert to a timestamp, then determine the month
     * and year to use when building the calendar
     */

    $ts = strtotime($this->_usedate);
    $this->_m = (int) date('m', $ts);
    $this->_y = (int) date('Y', $ts);

    /**
     * Determine how many days are in the month
     */

    $this->_daysInMonth = cal_days_in_month(
      CAL_GREGORIAN,
      $this->_m,
      $this->_y
    );

    /**
     * Determine what weekday the month start
     */

    $ts = mktime(0, 0, 0, $this->_m, 1, $this->_y);
    $this->_startDay = (int) date('w', $ts);
  }


  /**
   * Load event(s) info into an array
   * 
   * @param int $id an optional event ID to filter results
   * @return array an array of events from the database
   */

  private function _loadEventData($id = NULL)
  {

    
    $sql = " SELECT
                `event_id`, `event_title`, `event_desc`,`event_start`,`event_end`
              FROM `events`";

    /**
     * If an event ID is supplied add WHERE clause
     * sop only that event is returned
     */

    if (!empty($id)) {
      $sql .= "WHERE `event_id`=:id LIMIT 1";
    }

    /**
     * Otherwise, load all events for month in use
     */
    else {

      
      /**
       * Find the first and last days of the month
       */
      $start_ts = mktime(0, 0, 0, $this->_m, 1, $this->_y);
      $end_ts = mktime(23, 59, 59, $this->_m + 1, 0, $this->_y);
      $start_date = date('Y-m-d H:i:s', $start_ts);
      $end_date = date('Y-m-d H:i:s', $end_ts);
      
      /**
       * Filter events to only those happening in the
       * currently selected month
       */
      $sql .= "WHERE `event_start`
      BETWEEN '$start_date'
      AND '$end_date'
      ORDER BY `event_start`";
      
      
    }

    try {
      
      $stmt = $this->db->prepare($sql);

      /**
       * Bind the parameter if an ID was passed
       * 
       */
      if (!empty($id)) 
      {
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      }

      $stmt->execute();
      
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      
      $stmt->closeCursor();

      return $result;

    }
    catch (Exception $e) 
    {
      die($e->getMessage());
    }
  }

  /**
   * Loads all event for the month into an array
   * 
   * @return array events info
   */
   private function _createEventObj()
  {
    /**
     * Load event array 
     */
    
    $arr = $this->_loadEventData();

    /**
     * Create a new array, then organize the events
     * by the day of the month on which they occur
     */  

    $events = array();
    foreach ($arr as $event) {
      $day = date('j', strtotime($event['event_start']));
      
      try 
      {
        $events[$day][] = new Event($event);
      } 
      catch (Exception $e) 
      {
        die($e->getMessage());
      }
    }
    
    print_r($events);

    return $events;


  }

  /**
   * Return HTML to display hte caleendar and events
   *  
   * Using the information stored in classs properties, the
   * evets for the given month are loaded, the calendar is
   * generated, and the whole thing is returned as valid markup.
   * 
   * @return string the calendar HTML markup
   */


  public function buildCalendar()
  {

    /**
     * Determione the calendar moth and create an array of
     * weekday abbreviation to label the calendar colums
     */

    $cal_month = date('F Y', strtotime($this->_usedate));
    define('WEEKDAYS', array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'));


    /** 
     * Add a header to the calendar markup
     */
    $html = "\n\t<h2>$cal_month</h2>";
    for ($d = 0, $labels = NULL; $d < 7; ++$d) {
      $labels .= "\n\t\t<li>" . WEEKDAYS[$d] . "</li>";
    }

    $html .= "\n\t<ul class=\"weekdays\">" . $labels . "\n\t</ul>";

    /**
     * Load events data
     */
    $events = $this->_createEventObj();

    /**
     * Create the calndar markup
     */

    $html .= "\n\t<ul>";
    for (
      $i = 1, $c = 1, $t = date('j'), $m = date('m'), $y = date('Y');
      $c <= $this->_daysInMonth;
      ++$i
    ) 
    {
      /**
       * Apply a "fill" class to the boxes occuring before
       * the first of the month
       */
      $class = $i <= $this->_startDay ? "fill" : NULL;

      /**
       * Add a "today" class if the current fdate matches
       * the current date  
       */

      if ($c == $t && $m == $this->_m && $y == $this->_y) 
      {
        $class = "today";
      }

      /**
       * Building the opening and closing list item tags
       */

      $ls = sprintf("\n\t\t<li class=\"%s\">", $class);
      $le = "\n\t\t</li>";

      /**
       *Add the day of the month to identify the calendar box 
       */
      $event_info= NULL;

      if ($this->_startDay < $i && $this->_daysInMonth >= $c) 
      {

        /** 
         * Format events data
         */
         //clear the variable

        if (isset($events[$c]))
        {
          foreach ($events[$c] as $event)
          {
            $link = '<a href="view.php?event_id=' . $event->id . '">' . $event->title . '</a>';
            $event_info = "\n\t\t\t$link";
            
          }
          
        }
     
        $date = sprintf("\n\t\t\t<strong>%02d</strong>", $c++);
        }
        else
        {
        $date = "&nbsp; ";
        }

      /**
       * If the current day is a Saturday, wrap to the nex row
       */
      $wrap = $i != 0 && $i % 7 == 0 ? "\n\t</ul>\n\n<ul>" : NULL;

      
      /**
       * Assemble the pieces into a finished item
       */

      $html .= $ls . $date . $event_info . $le . $wrap;
       
    }

    /**
       * Add filler to finish out the last week
       */

      while ($i % 7 != 1) {
        $html .= "\n\t\t<li class=\"fill\">&nbsp;</li>";
        ++$i;
      }

      /**
       * Close the fineal unorderd list
       */
      $html .= "\n\t</ul>\n\n";

    /**
     * Return the markup for output
     */
    return $html;
    
  }


  /**
   * Displays a given event's information
   * 
   * @param in $id the event id
   * @return string basic markup to display the event info 
   */

   public function displayEvent($id)
   {
     /**
      * Make sure an id was passed
      */
      if (empty($id)) {return NULL;}

      /**
       * Make sure the ID is integer
       */

       $id = preg_replace('/[^0-9]/','',$id);

       /**
        * Load the event data from DB
        */

        $event = $this->_loadEventById($id);

        /**
         * Generate strings for the date, start, and end time
         */

         $ts = strtotime($event->start);
         $date  = date('F d, Y', $ts);
         $start = date('g:ia', $ts);
         $end = date('g:ia', strtotime($event->end));

         /**
          * generate and return the markup
          */

          return "<h2>$event->title</h2>"
                . "\n\t<p class=\"dates\">$date, $start&mdash;$end</p>"
                . "\n\t<p>$event->description</p>";

   }




  /**
   * Return a single evet object
   * 
   * @param int $id an event ID
   * @return  object the event object
   */

   private function _loadEventById($id)
   {
     /**
      * If no ID is passed, return NULL
      */

      if (empty($id))
      {
        return NULL;
      }

      /**
       * Load the events info array
       */
      $event = $this->_loadEventData($id);

      /**
       * Return an event object
       */

       if (isset($event[0]))
       {
         return new Event($event[0]);
       }
       else
       {
         return NULL;
       }
   }
}

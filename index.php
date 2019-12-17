<?php

/**
 * index.php
 */

declare(strict_types=1);

/**
 * Include necessary file
 */

include_once 'init.inc.php';

/**
 * Load calendar fron January
 */
$cal = new Calendar($dbo, "2019-01-01 12:00:00");

/**
 * Set up the page title and CSS files
 */

$page_title = "Event Calendar";
$css_file = array('style.css');

/**
 * Include the header
 */
include_once 'header.inc.php';

?>

<div id="content">

<?php

/**
 * Display the calendar HTML 
 */

echo $cal->buildCalendar();

?>

</div> 

<?php

include_once 'footer.inc.php';

?>
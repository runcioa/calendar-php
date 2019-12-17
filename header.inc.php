<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?php echo $page_title; ?></title>
  <?php print_r($css_file); ?>
  <?php foreach( $css_file as $css): ?>
  <link rel="stylesheet" type="text/css"  href="<?php echo $css ?>" />
  <?php endforeach; ?>

</head>
<body>
  

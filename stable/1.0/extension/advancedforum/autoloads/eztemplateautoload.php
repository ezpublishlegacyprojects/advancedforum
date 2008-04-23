<?php

// Operator autoloading

$eZTemplateOperatorArray = array();

$eZTemplateOperatorArray[] =
  array( 'script' => eZExtension::baseDirectory() . '/advancedforum/autoloads/topauthorsoperators.php',
         'class' => 'TopAuthorsOperators',
         'operator_names' => array( 'topauthors') );
$eZTemplateOperatorArray[] =
  array( 'script' => eZExtension::baseDirectory() . '/advancedforum/autoloads/splitlongwordsoperators.php',
         'class' => 'splitlongwordsOperators',
         'operator_names' => array( 'splitlongwords') );
?>

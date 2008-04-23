<?php
$Module = array( "name" => "View",
                 "variable_params" => true,
                 "function" => array(
                 "script" => "count.php",
                 "params" => array( 'NodeID' ) ) );

$ViewList = array();
$ViewList["count"] = array(
    "script" => "count.php",
    'params' => array( 'NodeID' ) );

?>

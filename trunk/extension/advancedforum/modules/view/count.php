<?php

$Module =& $Params['Module'];

if ( $Params[ 'NodeID' ] )
{
	include_once( "kernel/classes/ezviewcounter.php");
	$counter = eZViewCounter::fetch( $Params[ 'NodeID' ] );
	if ( !is_object( $counter ) )
	{
		$counter = eZViewCounter::create( $Params[ 'NodeID' ] );
	}
	$counter->increase();
}
eZExecution::cleanExit();
?>
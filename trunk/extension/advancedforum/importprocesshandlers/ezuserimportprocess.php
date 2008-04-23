<?php
class eZUserImportProcess extends eZImportProcess
{
	function eZUserImportProcess()
	{
		parent::eZImportProcess();
	}
	function &run( &$data, $namespace )
	{
		
		foreach ( $data as $row )
		{
			
			if ( $row === null )
				continue;

			$oldkeyvalue = $row['user_id'];
			$processHandlerImp = eZImportProcess::instance( 'ezcontentobject', $this->options );

			$array = array ( $row['co'] );
			$result = $processHandlerImp->run( $array );
			
			unset( $processHandlerImp );
			
			if ( !is_object( $result[0] ) )
				continue;
			$eZKeyConverter =& eZKeyConverter::instance();
			$eZKeyConverter->register( $this->namespace.'_owner_id', $oldkeyvalue, $result[0]->attribute( 'id' ) );
			
		}
	}
}
?>
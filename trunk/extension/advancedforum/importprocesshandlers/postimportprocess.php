<?php
class postImportProcess extends eZImportProcess
{
	function postImportProcess()
	{

	}
	function &run( &$data, $namespace )
	{
		$eZKeyConverter =& eZKeyConverter::instance();

		foreach ( $data as $row )
		{
			if ( $row === null )
				continue;
			$oldkeyvalue  = $row['topic_id'];

			$this->options[EZ_IMPORT_PRESERVED_KEY_OWNER_ID] = $eZKeyConverter->convert( $namespace."_owner_id", $row['poster_id'] );
			$this->options[EZ_IMPORT_PRESERVED_KEY_PARENT_NODE_ID] = $eZKeyConverter->convert( $namespace."_topic_node_id", $row['topic_id'] );
			$processHandlerImp = eZImportProcess::instance( 'ezcontentobject', $this->options );

			$array = array ( $row['co'] );
			$result = $processHandlerImp->run( $array );
			
			unset( $processHandlerImp );
			unset( $row );
		}
	}
}
?>
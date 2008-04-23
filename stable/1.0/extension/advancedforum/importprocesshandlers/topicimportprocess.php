<?php
class topicImportProcess extends eZImportProcess
{
	function topicImportProcess()
	{

	}
	function &run( &$data, $namespace )
	{
		$eZKeyConverter =& eZKeyConverter::instance();

		foreach ( $data[$namespace."_topic"] as $row )
		{
			if ( $row === null )
				continue;
			$oldkeyvalue  = $row['topic_id'];

			$this->options[EZ_IMPORT_PRESERVED_KEY_OWNER_ID] = $eZKeyConverter->convert( $namespace."_owner_id", $row['poster_id'] );
			$this->options[EZ_IMPORT_PRESERVED_KEY_PARENT_NODE_ID] = $eZKeyConverter->convert( $namespace."_forum_node_id", $row['forum_id'] );


			$processHandlerImp = eZImportProcess::instance( 'ezcontentobject', $this->options );

			$array = array ( $row['co'] );
			$result = $processHandlerImp->run( $array );
			
			unset( $processHandlerImp );
			unset( $row );
			if ( is_object( $result[0] ) )
			{
			    $eZKeyConverter->register( $this->namespace."_topic_node_id", $oldkeyvalue, $result[0]->attribute( 'main_node_id' ) );	
			}

		}
	}
}
?>
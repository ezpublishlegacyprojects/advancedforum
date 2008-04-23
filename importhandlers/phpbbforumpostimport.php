<?php
    
class PHPBBForumPostImportHandler extends eZImportFramework 
{
    function PHPBBForumPostImportHandler( $processHandler )
    {
        parent::eZImportFramework( $processHandler );
    }
    function getData( $source, $namespace = false )
    {
		$this->namespaces[] = $namespace;
		$this->namespaces = array_unique( $this->namespaces );
		if ( is_object( $source ) and is_a( $source, 'eZDBInterface' ) )
		{
			$this->data[$namespace] = $source->arrayQuery(
"SELECT post_subject, post_text, p.post_id, topic_id, forum_id,poster_id, post_time FROM ".$namespace."_posts p, ".$namespace."_posts_text pt
WHERE pt.post_id=p.post_id
ORDER BY p.topic_id, p.post_time asc"
			);
		}
    }
    function processData( $namespace )
    {
		$this->data[$namespace."_topic"] = array();
		
		$eZKeyCon = eZKeyConverter::instance();
    	for ( $i=0; $i < count ( $this->data[$namespace] ) ; $i++ )
    	{
    		unset( $new );
    		$new = array();
    		// no key preserved
    		if ( false === $eZKeyCon->hasRegister( $namespace."_topic", $this->data[$namespace][$i]['topic_id'] ) )
    		{
    			$this->data[$namespace."_topic"][$i] = $this->data[$namespace][$i];
    			$eZKeyCon->register( $namespace."_topic", $this->data[$namespace][$i]['topic_id'], true );
    			
    			$conv = new eZImportConverter( $this->data[$namespace][$i]['post_text'] );
    			$conv->addFilter( "bb2ez");
    			$conv2 = new eZImportConverter( $this->data[$namespace][$i]['post_subject'] );
    			$conv2->addFilter( "bb2ez");
    			$new[EZ_IMPORT_PRESERVED_KEY_CREATION_TIMESTAMP] = $this->data[$namespace][$i]['post_time'];
    			$new['message'] = $conv;
    			$new['subject'] = $conv2;
    			$this->data[$namespace."_topic"][$i]['co'] =& $new;
    			$this->data[$namespace][$i] = null;
    		}
    		else
    		{
    			$conv = new eZImportConverter( $this->data[$namespace][$i]['post_text'] );
    			$conv->addFilter( "bb2ez");
    			$conv2 = new eZImportConverter( $this->data[$namespace][$i]['post_subject'] );
    			$conv2->addFilter( "bb2ez");
    			$new[EZ_IMPORT_PRESERVED_KEY_CREATION_TIMESTAMP] = $this->data[$namespace][$i]['post_time'];
    			$new['message'] = $conv;
    			$new['subject'] = $conv2;
    			$this->data[$namespace][$i]['co'] =& $new;
    		}
    	}
    }
}
?>
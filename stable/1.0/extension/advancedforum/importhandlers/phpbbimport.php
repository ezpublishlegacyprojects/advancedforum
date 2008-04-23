<?php
    
class PHPBBImportHandler extends eZImportFramework 
{
    function PHPBBImportHandler( $processHandler )
    {
        parent::eZImportFramework( $processHandler );
    }
    function getData( $source, $namespace = false )
    {
		if ( is_object( $source ) and is_a( $source, 'eZDBInterface' ) )
		{
			
			$this->data[$namespace] = $source->arrayQuery(
			"SELECT user_id, user_active, username, user_password, user_regdate, user_email, user_sig
			 FROM " . $namespace .
			"_users WHERE user_id > 1 and user_active = 1"
			);
		}
    }
    function processData( $namespace )
    {
    	$eZKeyConverter =& eZKeyConverter::instance();
    	for ( $i=0; $i < count ( $this->data[$namespace] ) ; $i++ )
    	{
    		unset( $new );
    		$db = eZDB::instance();
    		$result = $db->arrayQuery( "SELECT * FROM ezuser WHERE email = '". $this->data[$namespace][$i]['user_email'] ."' OR login= '". $this->data[$namespace][$i]['username'] ."'");
    		
    		if ( $result )
    		{
    			$result = $db->arrayQuery( "SELECT * FROM ezuser WHERE email = '". $this->data[$namespace][$i]['user_email'] ."' AND login= '". $this->data[$namespace][$i]['username'] ."'");

    			if ( $result )
    			{
					$eZKeyConverter->register( $namespace.'_owner_id',  $this->data[$namespace][$i]['user_id'], $result[0]['contentobject_id'] );
    				eZImportFramework::log( "USER EXISTS ( match email and password ) ASSUMING is #".$result[0]['contentobject_id'].": " . $this->data[$namespace][$i]['username'] . " - " . $this->data[$namespace][$i]['user_email'] );

					$this->data[$namespace][$i] = null;
					continue;
    			}
    			$result = $db->arrayQuery( "SELECT * FROM ezuser WHERE email = '". $this->data[$namespace][$i]['user_email'] ."'" );
    			if ( $result )
    			{		
					$eZKeyConverter->register( $namespace.'_owner_id',  $this->data[$namespace][$i]['user_id'], $result[0]['contentobject_id'] );
    				eZImportFramework::log( "USER EXISTS( match email ) ASSUMING is #".$result[0]['contentobject_id'].": " . $this->data[$namespace][$i]['username'] . " - " . $this->data[$namespace][$i]['user_email'] );
					$this->data[$namespace][$i] = null;
					continue;
    			}
    			$this->data[$namespace][$i] = null;
    			eZImportFramework::log( "USER EXISTS but no full match: " . $this->data[$namespace][$i]['username'] . " - " . $this->data[$namespace][$i]['user_email'] );
    			continue;
    		}
    		
    		$new = array();
    		$new['signature'] = $this->data[$namespace][$i]['user_sig'];
    		$new['first_name'] ='';
    		$new['last_name'] = $this->data[$namespace][$i]['username'];
    		$new['user_account']['login'] = $this->data[$namespace][$i]['username'];
    		$new['user_account']['email'] = $this->data[$namespace][$i]['user_email'];
    		$new['user_account']['password_hash'] = $this->data[$namespace][$i]['user_password'];
    		$new['user_account']['password_hash_type'] = EZ_USER_PASSWORD_HASH_MD5_PASSWORD;

    		$this->data[$namespace][$i]['co'] = $new;
    	}
    	
    }
}
?>
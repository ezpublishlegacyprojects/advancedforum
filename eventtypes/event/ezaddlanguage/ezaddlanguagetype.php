<?php
include_once( "kernel/classes/ezworkflowtype.php" );
include_once( "lib/ezutils/classes/ezuri.php" );



define( "EZ_WORKFLOW_TYPE_ADDLANGUAGE_ID", "ezaddlanguage" );

class ezaddlanguageType extends eZWorkflowEventType
{
	function ezaddlanguageType()
	{
		$this->eZWorkflowEventType( EZ_WORKFLOW_TYPE_ADDLANGUAGE_ID, ezi18n( 'kernel/workflow/event', "ezaddlanguage" ) );
		$this->setTriggerTypes( array( 'content' => array( 'read' => array( 'before' ) ) ) );
	}

	function execute( &$process, &$event )
	{
            $processParameters = $process->attribute( 'parameter_list' );
	    if ( isset( $processParameters['node_id'] ) )
        {
		$db =& eZDB::instance();
		$result = $db->arrayQuery( "SELECT ec.identifier FROM ezcontentobject_tree e, ezcontentobject e1, 
ezcontentclass ec WHERE e.contentobject_id = e1.id 
AND e1.contentclass_id = ec.id AND e.node_id = " . $processParameters['node_id'] );

            $object =& eZContentObjectTreeNode::fetch( $processParameters['node_id'] );
	    	if ( isset( $result[0]['identifier'] ) and in_array( $result[0]['identifier'] , array( 'forum', 'forum_topic', 'forum_reply' ) ) )
	    	{
		      	$langs = eZContentLanguage::prioritizedLanguageCodes();
	    		$ini = eZINI::instance("forum.ini");
	    		$langs = array_merge( $langs, $ini->variable( "ForumSettings", 'ViewLanguages' ) );
	    		$langs = array_unique( $langs );
	    		eZContentLanguage::setPrioritizedLanguages($langs);
            }
		    else
		    {
                eZDebug::writeDebug("Identifier not 'forum', 'forum_topic', 'forum_reply' ");
		    }
        }
	    return EZ_WORKFLOW_TYPE_STATUS_ACCEPTED;
	}
}

eZWorkflowEventType::registerType( EZ_WORKFLOW_TYPE_ADDLANGUAGE_ID, "ezaddlanguagetype" );

?>
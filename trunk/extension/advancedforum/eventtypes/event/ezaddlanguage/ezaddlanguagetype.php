<?php

class ezaddlanguageType extends eZWorkflowEventType
{
	const WORKFLOW_TYPE_STRING = 'ezaddlanguage';
	
	function ezaddlanguageType()
	{
		$this->eZWorkflowEventType( ezaddlanguageType::WORKFLOW_TYPE_STRING, ezi18n( 'kernel/workflow/event', "Add Language" ) );
		$this->setTriggerTypes( array( 'content' => array( 'read' => array( 'before' ) ) ) );
	}

	function execute( $process, $event )
	{
        $processParameters = $process->attribute( 'parameter_list' );
	    if ( isset( $processParameters['node_id'] ) )
        {
		$db = eZDB::instance();
		$result = $db->arrayQuery( "SELECT ec.identifier FROM ezcontentobject_tree e, ezcontentobject e1, 
ezcontentclass ec WHERE e.contentobject_id = e1.id 
AND e1.contentclass_id = ec.id AND e.node_id = " . $processParameters['node_id'] );

            $object = eZContentObjectTreeNode::fetch( $processParameters['node_id'] );
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
	    return eZWorkflowType::STATUS_ACCEPTED;
	}
}

eZWorkflowEventType::registerEventType( ezaddlanguageType::WORKFLOW_TYPE_STRING, 'ezaddlanguageType' );

?>

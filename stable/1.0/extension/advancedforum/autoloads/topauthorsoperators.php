<?php

class TopAuthorsOperators
{
    /*!
     Constructor
    */
    function TopAuthorsOperators()
    {
        $this->Operators = array( 'topauthors');
    }

    /*!
     Returns the operators in this class.
    */
    function &operatorList()
    {
        return $this->Operators;
    }

    /*!
     \return true to tell the template engine that the parameter list
    exists per operator type, this is needed for operator classes
    that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     The first operator has three parameters.
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return array( 'topauthors' => array( 'class' => array( 'type' => 'array',
                                                                  'required' => true,
                                                                  'default' => false ),
                                             'offset' => array ( 'type' => 'int',
                                                                 'required' => true,
                                                                 'default' => '0' ),
                                             'limit' => array ( 'type' => 'int',
                                                                'required' => true,
                                                                'default' => '10' ),                                                                
                                                                
                                                                ) );
    }

    /*!
     Executes the needed operator(s).
     Checks operator names, and calls the appropriate functions.
    */
    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace,
                     &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'topauthors':
            {
            	if ( !is_array( $namedParameters['class'] ) or count ( $namedParameters['class'] ) < 1 )
            	{
            		return;
            	}
            	
                $db =& eZDB::instance();                
                
                $query="SELECT `owner_id` AS user_id, count( `owner_id` ) AS count
                        FROM `ezcontentobject` 
                        WHERE `contentclass_id` = '".(int)$namedParameters['class_id']."'
                        AND `status` = '1'
                        GROUP BY `user_id` 
                        ORDER BY `count` DESC";
               
                $ResultArray =& $db->arrayQuery( $query, array( "offset" => $namedParameters['offset'],
                                                                "limit" => $namedParameters['limit'] ) );                
                $operatorValue = $ResultArray;
                
            } break;
        }
    }

    /// \privatesection
    var $Operators;
}

?>
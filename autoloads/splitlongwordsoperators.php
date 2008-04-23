<?php

class splitlongwordsOperators
{
    /*!
     Constructor
    */
    function splitlongwordsOperators()
    {
        $this->Operators = array( 'splitlongwords');
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
        return array( 'splitlongwords' => array( 'length' => array( 'type' => 'string',
                                                                  'required' => true,
                                                                  'default' => 20 )                                                               
                                                                
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
            case 'splitlongwords':
            {   
                $operatorValue = preg_replace( '/([^\s]{'.$namedParameters['length'].'})(?=[^\s])/', '$1&#8203;', $operatorValue );
            } break;
        }
    }

    /// \privatesection
    var $Operators;
}

?>
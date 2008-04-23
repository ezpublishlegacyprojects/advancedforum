<?php

/*! \file function_definition.php
*/

$FunctionList = array();

$FunctionList['items'] = array( 'name' => 'items',
                                'call_method' => array( 'include_file' => 'extension/mobotix/modules/download/ezdownloadfunctioncollection.php',
                                                        'class' => 'ezdownloadfunctioncollection',
                                                        'method' => 'items' ),
                                'parameter_type' => 'standard',
                                'parameters' => array( array( 'name' => 'node_id',
                                                              'type' => 'string',
                                                              'required' => true ) ) );
                                                               



?>

<?php
$readme  = <<< README

README:
This script currently imports from 2 different phpbb setups into one eZ publish.
You may need to customize this script, while the import classes should stay untouched.
This scrip tis dependant on the extension IMPORT from the PUBSVN.
README;
include_once( 'lib/ezutils/classes/ezcli.php' );
include_once( 'kernel/classes/ezscript.php' );
$cli =& eZCLI::instance();
$script =& eZScript::instance( array( 'description' => ( "PHPBB import script. Find more information on phpbb on http://www.phpbb.com/\n" . $readme .
                                                         "\n" .
                                                         "./extension/advancedforum/bin/phpbb2ez.php --type=mysql --database=db_forum1 --host=localhost --user=root --password=secret" ),
                                      'use-session' => true,
                                      'use-modules' => true,
                                      'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions( "[type:][user:][host:][password:][socket:][database:][charset:][root:]",
                                "",
                                array( 'type' => ( "Which database type to use, can be one of:\n" .
                                                   "mysql, postgresql or any other supported by extensions" ),
                                       'host' => "Connect to host source database",
                                       'user' => "User for login to source database",
                                       'password' => "Password to use when connecting to source database",
                                       'socket' => 'Socket to connect to match and source database (only for MySQL)',
                                       'table-type' => ( "The table storage type to use for SQL output when creating tables.\n" .
                                                         "MySQL: bdb, innodb and myisam\n" .
                                                         "PostgreSQL: \n" .
                                                         "Oracle: " ),
                                       'clean-existing' => 'Clean up existing schema (remove all database objects)',
                                       'charset' => 'Defines the charset to use on tables, the names of the charset depends on database type',
                                       'schema-file' => 'The schema file to use when dumping data structures, is only required when dumping from files',
                                       'allow-multi-insert' => ( 'Will create INSERT statements with multiple data entries (applies to data output only)' . "\n" .
                                                                 'Multi-inserts will only be created for databases that support it' ),
                                       'insert-types' => ( "A comma separated list of types to include in dump (default is schema only):\n" .
                                                           "schema - Table schema\n" .
                                                           "data - Table data\n" .
                                                           "all - Both table schema and data\n" .
                                                           "none - Insert nothing (useful if you want to clean up schema only)" )
                                       ) );

$script->initialize();


$sys =& eZSys::instance();

if ( empty( $options ) )
{
	$script->shutdown();
	$script->showHelp();
}

$type = $options['type'];
$host = $options['host'];
$user = $options['user'];
$socket = $options['socket'];
$password = $options['password'];
$database = $options['database'];

if ( !is_string( $password ) )
    $password = '';
// Connect to database

include_once( 'lib/ezdb/classes/ezdb.php' );

//eZ database
$db = eZDB::instance();

$parameters = array( 'server' => $host,
                     'user' => $user,
                     'charset' => $options['charset'],
                     'use_defaults' => true,
                     'password' => $password,
                     'database' => $database );
if ( $socket )
    $parameters['socket'] = $socket;

$dbsource =& eZDB::instance( $type,
                       $parameters,
                       true );

if ( !is_object( $dbsource ) )
{
    $cli->error( 'Could not initialize database:' );
    $cli->error( '* No database handler was found for $type' );
    $script->shutdown( 1 );
}
if ( !$dbsource or !$dbsource->isConnected() )
{
    $cli->error( "Could not initialize database" );

    // Fetch the database error message if there is one
    // It will give more feedback to the user what is wrong
    $msg = $dbsource->errorMessage();
    if ( $msg )
    {
        $number = $dbsource->errorNumber();
        if ( $number > 0 )
            $msg .= '(' . $number . ')';
        $cli->error( '* ' . $msg );
    }
    $script->shutdown( 1 );
}

$cli->output( 'Using Siteaccess '.$GLOBALS['eZCurrentAccess']['name'] );

// login as admin
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
$user = eZUser::fetchByName( 'admin' );

if ( is_object( $user ) )
{
	if ( $user->loginCurrent() )
	   $cli->output( "Logged in as 'admin'" );
}
else
{
	$cli->error( 'No admin.' );
    $script->shutdown( 1 );
}
include_once( "kernel/classes/ezcontentobject.php" );
include_once( "kernel/classes/ezcontentclass.php" );
include_once( 'lib/ezutils/classes/ezextension.php' );
ext_class( 'import' ,  'ezimportframework' );

$root = eZContentObjectTreeNode::fetch( $options['root'] );


$ini = eZINI::instance();

$eZKeyCon =& eZKeyConverter::instance();
$eZKeyCon->setDefault( 'phpbb_users'.'_owner_id', $user->attribute( 'contentobject_id' ) );
$eZKeyCon->setDefault( 'phpbb_en_users'.'_owner_id', $user->attribute( 'contentobject_id' ) );
$eZKeyCon->setDefault( 'parent_node_id', $ini->variable( "UserSettings", "DefaultUserPlacement" ) );


$options = array (
	 EZ_IMPORT_PRESERVED_KEY_CLASS_ID => $ini->variable( "UserSettings", "UserClassID" ),
	 EZ_IMPORT_PRESERVED_KEY_PARENT_NODE_ID => $ini->variable( "UserSettings", "DefaultUserPlacement" ),
	 EZ_IMPORT_PRESERVED_KEY_OWNER_ID => $user->attribute( 'contentobject_id' )
);
/* IMPORT USERS */
$if =& eZImportFramework::instance( 'phpbb' );
$if->getData( $dbsource, "phpbb" );
$if->processData( 'phpbb' );
$if->importData( 'ezuser', 'phpbb', $options );
$cli->output( "User de imported" );
$if->freeMem();

$if->getData( $dbsource, "phpbb_en" );
$if->processData( 'phpbb_en' );
$if->importData( 'ezuser', 'phpbb_en', $options );
$cli->output( "User en imported" );
$if->freeMem();


/* forums to be compeleted, not needed for now since we did import to specific forums.

$if =& eZImportFramework::instance( 'phpbbforum' );
$if->getData( $dbsource, "phpbb_forums" );
$if->processData( 'phpbb_forums', array( 'map' => array( 'forum_name' => 'name', 'forum_desc' => 'description' ) ) );
$options = array (
	'contentClassID' => 'forum',
	'parentNodeID' => $root->attribute("node_id"),
	'access' => 'ger'
);
$if->importData( 'eZForum', 'phpbb_forums', $options );
$cli->output( "Forums imported" );

*/

/*
$data = <<< TEST
Hello! This is a test string
[quote:a0e95bfbae="daniel74"]
Unter [url]www.pfalzstorch.de/bilder/live1.html[/url] liefert eine MOBOTIX-Kamera Live-Bilder von einem Storchennest in der Pfalz.
Sehr schöne Cam !  [i:6897d57f32]user[/i:6897d57f32]-Passwort
Hat die M10Web die Temperatur Anzeige schon dabei ? Oder hast Du das nachgerüstet? Wenn ja: wie und wo?[/quote:a0e95bfbae]

Nein, das ist die interne Temperatur. Da ziehe ich einfach 12,5 °C ab.
[quote:a0e95bfbae="daniel74"]
[code:1:6897d57f32]get &lt;benutzer&gt;&#58;&lt;kennwort&gt;@/control/control?set&amp;section=eventcontrol&amp;motioncheck=0 http/1.0[/code:1:6897d57f32] - leider ohne Erfolg.

Sehr schöne Cam !
Hat die M10Web die Temperatur Anzeige schon dabei ? Oder hast Du das nachgerüstet? Wenn ja: wie und wo?[/quote:a0e95bfbae]
[img:d6d7b85aaa]http://cam.mannheim-wetter.info/cam1/mannheim-himmel-s.jpg[/img:d6d7b85aaa]
Nein, das ist die interne Temperatur. Da ziehe ich einfach 12,5 °C ab.

Mehr unter [url=http://cam.mannheim-wetter.info/] [b:d6d7b85aaa]SkyCam Mannheim[/b:d6d7b85aaa]

[color=blue:51c3bff6c8][/color:51c3bff6c8] Have you

Hilfe:
[quote:9460852997]Die wesentliche Weiterentwicklung der Software besteht darin, die bisherige Speicherung von Einzelbildern (Voralarm-, Alarm- und Nachalarmbilder) in ein auf dem MOBOTIX-Streamingformat MxPEG basierendes Video-Clip-Verfahren zu überführen. [b:9460852997]Die bisherige Funktionalität kann weitgehend weiter verwendet werden[/b:9460852997]. Zusätzlich ist die Daueraufzeichung und die ereignisgesteuerte Aufzeichnung von Videosequenzen möglich. Aufzeichnung im MxPEG-Format ermöglicht auch, den vom Kameramikrofon aufgezeichneten Ton zu speichern[/quote:9460852997]

Unter [url]www.pfalzstorch.de/bilder/live1.html[/url] liefert eine MOBOTIX-Kamera Live-Bilder von einem Storchennest in der Pfalz.
TEST;

$conv = new eZImportConverter( $data );
$conv->addFilter( "bb2ez");
echo ($conv->run());
die();*/

/* IMPORT FORUM TOPICS DE */
$if =& eZImportFramework::instance( 'phpbbforumpost' );
$if->getData( $dbsource, "phpbb" );
$if->processData( 'phpbb', array( 'map' => array( 'forum_name' => 'name', 'forum_desc' => 'description' ) ) );
$options = array (
	#'access' => 'ger',
	 EZ_IMPORT_LANGUAGE_TAG => 'ger-DE',
	 EZ_IMPORT_PRESERVED_KEY_CLASS => 'forum_topic',
	'ignore_data_namespace' => true
);

$eZKeyCon->data['phpbb_forum_node_id'][eZKeyConverter::generateKey( 9 )] = 1447;
$eZKeyCon->data['phpbb_en_forum_node_id'][eZKeyConverter::generateKey( 2 )] = 1447;

$eZKeyCon->data['phpbb_forum_node_id'][eZKeyConverter::generateKey( 11 )] = 1448;
$eZKeyCon->data['phpbb_en_forum_node_id'][eZKeyConverter::generateKey( 3 )] = 1448;

$eZKeyCon->data['phpbb_forum_node_id'][eZKeyConverter::generateKey( 12 )] = 1449;
$eZKeyCon->data['phpbb_en_forum_node_id'][eZKeyConverter::generateKey( 4 )] = 1449;

$eZKeyCon->data['phpbb_forum_node_id'][eZKeyConverter::generateKey( 13 )] = 1450;
$eZKeyCon->data['phpbb_en_forum_node_id'][eZKeyConverter::generateKey( 5 )] = 1450;

$eZKeyCon->data['phpbb_forum_node_id'][eZKeyConverter::generateKey( 15 )] = 1451;
$eZKeyCon->data['phpbb_en_forum_node_id'][eZKeyConverter::generateKey( 6 )] = 1451;

$eZKeyCon->data['phpbb_forum_node_id'][eZKeyConverter::generateKey( 18 )] = 1452;
$eZKeyCon->data['phpbb_en_forum_node_id'][eZKeyConverter::generateKey( 7 )] = 1452;

$eZKeyCon->data['phpbb_forum_node_id'][eZKeyConverter::generateKey( 19 )] = 1453;
$eZKeyCon->data['phpbb_en_forum_node_id'][eZKeyConverter::generateKey( 8 )] = 1453;

$eZKeyCon->data['phpbb_forum_node_id'][eZKeyConverter::generateKey( 20 )] = 1454;
$eZKeyCon->data['phpbb_en_forum_node_id'][eZKeyConverter::generateKey( 9 )] = 1454;

$eZKeyCon->data['phpbb_forum_node_id'][eZKeyConverter::generateKey( 17 )] = 1455;
$eZKeyCon->data['phpbb_en_forum_node_id'][eZKeyConverter::generateKey( 10 )] = 1455;

$eZKeyCon->data['phpbb_forum_node_id'][eZKeyConverter::generateKey( 14 )] = 1455;


$eZKeyCon->setDefault( 'phpbb_forum_node_id', 1488 );
$eZKeyCon->setDefault( 'phpbb_en_forum_node_id', 1488 );
$eZKeyCon->setDefault( 'phpbb_topic_node_id', 1488 );
$eZKeyCon->setDefault( 'phpbb_en_topic_node_id', 1488 );
$if->importData( 'topic', 'phpbb', $options );
$cli->output( "Forum topic de imported" );
$options = array (
	 EZ_IMPORT_LANGUAGE_TAG => 'ger-DE',
	 EZ_IMPORT_PRESERVED_KEY_CLASS => 'forum_reply',
	'ignore_data_namespace' => false
);
$if->importData( 'post', 'phpbb', $options );
$cli->output( "Forum post de imported" );

/* IMPORT FORUM TOPICS EN */
$if =& eZImportFramework::instance( 'phpbbforumpost' );
$if->getData( $dbsource, "phpbb_en" );
$if->processData( 'phpbb_en', array( 'map' => array( 'forum_name' => 'name', 'forum_desc' => 'description' ) ) );
$options = array (
	 EZ_IMPORT_PRESERVED_KEY_CLASS => 'forum_topic',
	'ignore_data_namespace' => true
);
$if->importData( 'topic', 'phpbb_en', $options );
$cli->output( "Forum topic en imported" );

$options = array (
	 EZ_IMPORT_PRESERVED_KEY_CLASS => 'forum_reply',
	'ignore_data_namespace' => false
);
$if->importData( 'post', 'phpbb_en', $options );
$cli->output( "Forum post en imported" );

$if->destroy();

return $script->shutdown();

?>

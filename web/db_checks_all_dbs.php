<?php
#
# Backup checks etc
#
# $Id: //Infrastructure/GitHub/Database/asbo/web/db_checks_all_dbs.php#7 $
#

function __autoload($class_name) {
    include 'inc/' . $class_name . '.class.php';
}

#if( @$_GET['html'] ){
if( u::request_val('html') ){
    u::p('<!doctype html>');
    u::p('<html>');
    u::p('<head>');
    u::p('</head>');
    u::p('<body>');    
    $break='<br>';
}else{
    $break="\n";
}
echo $break . 'Report time : ' . date(DATE_RFC2822) . $break;
#
# Get all connection details
#
include 'conf/db_lookup.php';
$db_lookup = simplexml_load_string($db_look_up_xml);
$inc_str   = u::request_val('inc_str');
$exc_str   = u::request_val('exc_str');

foreach($db_lookup as $key0 => $db_detail){
    $db = (string) $db_detail['name'];
    #
    # Run Checks?
    #
    $include_it = 1;
    if( $inc_str ){       
        $search_pos = stripos( $db, $inc_str );
        if( $search_pos === 0 or $search_pos > 0 ){
            $include_it = 1;
        }else{
            $include_it = 0;
       }
    }
    #
    # Now excludes
    #
    $exclude_it = 0;
    if( $exc_str ){
        $search_pos = stripos( $db, $exc_str );
        if( $search_pos === 0 or $search_pos > 0 ){
            $exclude_it = 1;
        }
    }
  
    #u::p( $db . ' Inc : ' . $include_it . ' Exc : ' . $exclude_it . '<br>' );

    if( $include_it and !$exclude_it ){ 

        echo $break."DB : $db - Connecting to : $db_detail->conn_str, user : $db_detail->user\n";
        u::flush();
        $db_obj = new db($db_detail->user, $db_detail->pw, $db_detail->conn_str, 1);

        $just_checks = 1;
        if( $db_obj->connection_error ){
            u::p('<span style="background-color:red;">CRITICAL</span>');
        }else{
            include 'db_checks.php'; 
        }

    }
}

if( @$_GET['html'] ){
    u::p('</body>');
    u::p('</html>');
}
?>

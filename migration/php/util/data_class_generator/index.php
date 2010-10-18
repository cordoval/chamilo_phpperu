<?php
/**
 * @package migration.util.data_class_generator
 * @author Sven Vanpoucke
 */
/**
 * Collect the different tables and columns from a database and generate dataclasses
 */

include (dirname(__FILE__) . '/settings.inc.php');
include (dirname(__FILE__) . '/dataclassgenerator.class.php');
include (dirname(__FILE__) . '/mytemplate.php');
ini_set('include_path', realpath(dirname(__FILE__) . '/../../../plugin/pear'));
require_once dirname(__FILE__) . '/../../../common/global.inc.php';

$information_schema = $connectionstring . 'information_schema';

$db_lcms = MDB2 :: connect($information_schema);

foreach ($databases as $database)
{
    echo ('Doing database ' . $database . '<br />');
    
    $tablenames = array();
    
    $query = 'SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA =\'' . $database . '\'';
    
    $result = $db_lcms->query($query);
    while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
    {
        $tablenames[] = $record['table_name'];
    }
    $result->free();
    
    foreach ($tablenames as $tablename)
    {
        $query = 'SELECT COLUMN_NAME FROM COLUMNS WHERE TABLE_SCHEMA =\'' . $database . '\'' . ' AND TABLE_NAME=\'' . $tablename . '\'';
        
        $columnames = array();
        
        $result = $db_lcms->query($query);
        while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $columnnames[] = $record['column_name'];
        }
        $result->free();
        
        //Has to be filled manually
        $classname = $classprefix . strtoupper(substr($tablename, 0, 1)) . substr($tablename, 1);
        $description = 'This class presents a ' . $classprefix . ' ' . $tablename;
        
        $generator = new DataClassGenerator($database, $classname, $columnnames, $package, $description, $author);
        
        foreach ($columnnames as $i => $columnname)
            unset($columnnames[$i]);
    
    }
    
    foreach ($tablenames as $i => $tablename)
        unset($tablenames[$i]);
    
    echo ('Finished database ' . $database . '<br /><br />');

}

?>
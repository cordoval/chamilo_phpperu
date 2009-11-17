<?php
/**
 * $Id: clean_up_script.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.util
 */

/**
 * Cleanupscript to clean the lcms database
 */

ini_set('include_path', realpath(dirname(__FILE__) . '/../../plugin/pear'));
require_once dirname(__FILE__) . '/../../common/global.inc.php';

$conf = Configuration :: get_instance();
$dsn = $conf->get_parameter('database', 'connection_string');
$pos = strrpos($dsn, "/");
$information_schema = substr($dsn, 0, $pos) . '/information_schema';

$db_lcms = MDB2 :: connect($information_schema);

$tablenames = array();

$query = 'SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA =\'lcms\'';
$result = $db_lcms->query($query);
while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
{
    $tablenames[] = $record['table_name'];
}
$result->free();

$db_lcms->disconnect();

$db_lcms = MDB2 :: connect($dsn);

foreach ($tablenames as $tablename)
{
    if ($tablename == 'admin_setting' || $tablename == 'admin_language')
    {
        continue;
    }
    
    echo ('Cleaning table ' . $tablename);
    $query = 'TRUNCATE ' . $tablename;
    $db_lcms->query($query);
    echo (' OK <br />');
}

?>

<?php
/**
 * $Id: count_deleted_courses.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.util
 */
/**
 * Script to see which courses are still in the database but not on the filesystem
 */

ini_set('include_path', realpath(dirname(__FILE__) . '/../plugin/pear'));
require_once dirname(__FILE__) . '/../common/global.inc.php';

$conf = Configuration :: get_instance();
$dsn = $conf->get_parameter('database', 'connection_string');
$pos = strrpos($dsn, "/");
$information_schema = substr($dsn, 0, $pos) . '/_8__claroMain';

$db_lcms = MDB2 :: connect($information_schema);

$coursenames = array();

$query = 'SELECT * FROM course';

$result = $db_lcms->query($query);

while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
{
    $coursenames[$record['code']] = $record['directory'];
    $i ++;
}
$result->free();

$db_lcms->disconnect();

$path = '/var/www/html/bron/courses/';
$directories = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);
foreach ($coursenames as $code => $directory)
{
    $key = array_search($directory, $directories);
    if ($key != null)
    {
        unset($directories[$key]);
    }
}

foreach ($directories as $directory)
{
    if ($directory != '.svn')
        echo ($directory . '<BR/>');
}
?>
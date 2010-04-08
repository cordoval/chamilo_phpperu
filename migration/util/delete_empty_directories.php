<?php
/**
 * $Id: delete_empty_directories.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.util
 */
/**
 * Script to delete empty directories
 */

ini_set('include_path', realpath(dirname(__FILE__) . '/../plugin/pear'));
require_once dirname(__FILE__) . '/../common/global.inc.php';

$conf = Configuration :: get_instance();

$path = '/home/svennie/sites/dokeos-lcms/migration/testmap/';
$directories = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, true);
rsort($directories);
foreach ($directories as $directory)
{
    $files = Filesystem :: get_directory_content($directory, Filesystem :: LIST_FILES_AND_DIRECTORIES, true);
    if (count($files) == 0)
    {
        Filesystem :: remove($directory);
        echo ($directory . '<br />');
    }
}

?>
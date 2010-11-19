<?php

$config = array();

require_once dirname(__FILE__) . '/config.inc.php';

mysql_connect($config['database_host'], $config['database_user'], $config['database_pass']);

$query = 'SELECT * FROM ' . $config['main_database'] . '.course;';
$result = mysql_query($query);

$database_deleted_files = 0;
$real_deleted_files = 0;

while($row = mysql_fetch_assoc($result))
{
    $course_database = $row['db_name'];
    
    $query = 'SELECT path FROM ' . $course_database . '.document WHERE path LIKE \'%deleted%\';';
    $document_result = mysql_query($query);
    
    while($count_row = mysql_fetch_assoc($document_result))
    {
        $database_deleted_files++;
        
        $path = $config['root_path'] . $row['directory'] . '/document/' . $count_row['path'];
        if(!file_exists($path))
        {
            var_dump($path);
            $real_deleted_files++;
        }
    }
}

echo 'Total: database deleted = ' . $database_deleted_files . ' - really deleted = ' . $real_deleted_files;

?>
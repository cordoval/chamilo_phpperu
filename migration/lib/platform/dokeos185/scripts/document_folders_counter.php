<?php

$config = array();

require_once dirname(__FILE__) . '/config.inc.php';

mysql_connect($config['database_host'], $config['database_user'], $config['database_pass']);

$query = 'SELECT * FROM ' . $config['main_database'] . '.course;';
$result = mysql_query($query);

$counter = 0;

while($row = mysql_fetch_assoc($result))
{
    $course_database = $row['db_name'];
    
    $query = 'SELECT COUNT(*) AS count FROM ' . $course_database . '.document WHERE filetype=\'folder\';';
    $document_result = mysql_query($query);
    $count_row = mysql_fetch_assoc($document_result);

    $count = $count_row['count'];
    $counter += $count;
     
    echo $course_database . ': ' . $count . "\n";
}

echo 'Total: ' . $counter;

?>
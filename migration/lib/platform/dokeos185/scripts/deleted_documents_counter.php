<?php

$config = array();

require_once dirname(__FILE__) . '/config.inc.php';

mysql_connect($config['database_host'], $config['database_user'], $config['database_pass']);

$query = 'SELECT * FROM ' . $config['main_database'] . '.course;';
$result = mysql_query($query);

while($row = mysql_fetch_assoc($result))
{
    var_dump($row); 
}

?>
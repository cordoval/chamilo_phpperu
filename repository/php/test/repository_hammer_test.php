<?php
/**
 * $Id: repository_hammer_test.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.test
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

header('Content-Type: text/plain');

$dataManager = RepositoryDataManager :: get_instance();
$objects = $dataManager->retrieve_content_objects();
while ($object = $objects->next_result())
{
    echo $object->get_id() . "\n";
}

?>
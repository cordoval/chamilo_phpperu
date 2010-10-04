<?php
/**
 * $Id: content_object.php 157 2009-11-10 13:44:02Z vanpouckesven $
 * @package common.javascript.ajax
 */
require_once dirname(__FILE__) . '/../../global.inc.php';

$object = Request :: post('object');
$object = RepositoryDataManager :: get_instance()->retrieve_content_object($object);

echo $object->get_title();

?>
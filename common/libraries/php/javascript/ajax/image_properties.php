<?php
/**
 * $Id: image_properties.php 155 2009-11-10 13:20:56Z vanpouckesven $
 * @package common.javascript.ajax
 */
require_once dirname(__FILE__) . '/../../global.inc.php';
require_once Path :: get_repository_path() . 'lib/content_object/document/document.class.php';

$object = Request :: post('content_object');
$object = RepositoryDataManager :: get_instance()->retrieve_content_object($object);

$full_path = $object->get_full_path();
$dimensions = getimagesize($full_path);

$properties = array();
$properties[ContentObject :: PROPERTY_ID] = $object->get_id();
$properties[ContentObject :: PROPERTY_TITLE] = $object->get_title();
$properties['fullPath'] = $full_path;
$properties['webPath'] = $object->get_url();
$properties[Document :: PROPERTY_FILENAME] = $object->get_filename();
$properties[Document :: PROPERTY_PATH] = $object->get_path();
$properties[Document :: PROPERTY_FILESIZE] = $object->get_filesize();
$properties['width'] = $dimensions[0];
$properties['height'] = $dimensions[1];
$properties['type'] = $object->get_extension();

echo json_encode($properties);
?>
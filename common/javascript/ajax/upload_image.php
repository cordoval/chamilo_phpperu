<?php
/**
 * $Id: upload_image.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.javascript.ajax
 */
require_once dirname(__FILE__) . '/../../global.inc.php';
require_once Path :: get_repository_path() . 'lib/content_object/document/document.class.php';

if (! empty($_FILES))
{
    $upload_path = Path :: get(SYS_REPO_PATH);
    $owner = Request :: post('owner');
    
    $filename = $_FILES['Filedata']['name'];
    $hash = md5($_FILES['Filedata']['name']);
    
    $path = $owner . '/' . Text :: char_at($hash, 0);
    $full_path = $upload_path . $path;
    
    Filesystem :: create_dir($full_path);
    $hash = Filesystem :: create_unique_name($full_path, $hash);
    
    $path = $path . '/' . $hash;
    $full_path = $full_path . '/' . $hash;
    
    move_uploaded_file($_FILES['Filedata']['tmp_name'], $full_path) or die('Failed to create "' . $full_path . '"');
    
    $document = new Document();
    $document->set_owner_id($owner);
    $document->set_parent_id(0);
    $document->set_path($path);
    $document->set_filename($filename);
    $document->set_filesize(Filesystem :: get_disk_space($full_path));
    $document->set_hash($hash);
    
    $title_parts = explode('.', $filename);
    $extension = array_pop($title_parts);
    $title = Utilities :: underscores_to_camelcase_with_spaces(implode('_', $title_parts));
    $document->set_title($title);
    $document->set_description($title);
    $document->create();

    $dimensions = getimagesize($full_path);
    
    $properties = array();
    $properties[ContentObject :: PROPERTY_ID] = $document->get_id();
    $properties[ContentObject :: PROPERTY_TITLE] = $document->get_title();
    $properties['fullPath'] = $full_path;
    $properties['webPath'] = $document->get_url();
    $properties[Document :: PROPERTY_FILENAME] = $document->get_filename();
    $properties[Document :: PROPERTY_PATH] = $document->get_path();
    $properties[Document :: PROPERTY_FILESIZE] = $document->get_filesize();
    $properties['width'] = $dimensions[0];
    $properties['height'] = $dimensions[1];
    $properties['type'] = $document->get_extension();
    $properties['owner'] = $owner;
    
    echo json_encode($properties);
}
?>
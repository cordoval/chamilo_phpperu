<?php
namespace repository\content_object\dailymotion;
/**
 * $Id: dailymotion_difference.class.php 2010-06-08
 * package repository.lib.content_object.dailymotion
 * @author Shoira Mukhsinova
 */
class DailymotionDifference extends ContentObjectDifference
{

    function get_difference()
    {
        $object = $this->get_object();
        $version = $this->get_version();
        
        $object_string = $object->get_url();
        $object_string = explode("\n", strip_tags($object_string));
        
        $version_string = $version->get_url();
        $version_string = explode("\n", strip_tags($version_string));
        
        $td = new Difference_Engine($version_string, $object_string);
        
        return array_merge(parent :: get_difference(), $td->getDiff());
    }
}
?>
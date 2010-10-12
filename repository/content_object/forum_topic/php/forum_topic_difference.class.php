<?php
namespace repository\content_object\forum_topic;
/**
 * $Id: forum_topic_difference.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum_topic
 */
/**
 * This class can be used to get the difference between forum topics
 */
class ForumTopicDifference extends ContentObjectDifference
{

    function get_difference()
    {
        $object = $this->get_object();
        $version = $this->get_version();
        
        $object_string = $object->get_locked();
        $object_string = explode("\n", strip_tags($object_string));
        
        $version_string = $version->get_locked();
        $version_string = explode("\n", strip_tags($version_string));
        
        $td = new Difference_Engine($version_string, $object_string);
        
        return array_merge(parent :: get_difference(), $td->getDiff());
    }
}
?>
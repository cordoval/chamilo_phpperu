<?php
/**
 * $Id: complex_forum_topic.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum_topic
 */

class ComplexForumTopic extends ComplexContentObjectItem
{
    const PROPERTY_TYPE = 'type';

    function get_type()
    {
        return $this->get_additional_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_additional_property(self :: PROPERTY_TYPE, $type);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_TYPE);
    }

    function create()
    {
        parent :: create();
        
        $rdm = RepositoryDataManager :: get_instance();
        $lo = $rdm->retrieve_content_object($this->get_ref());
        
        $parent = $rdm->retrieve_content_object($this->get_parent());
        $parent->add_topic();
        $parent->add_post($lo->get_total_posts());
        $parent->recalculate_last_post($this->get_ref());
        
        return true;
    }

    function delete()
    {
        parent :: delete();
        
        $rdm = RepositoryDataManager :: get_instance();
        $lo = $rdm->retrieve_content_object($this->get_ref());
        
        $parent = $rdm->retrieve_content_object($this->get_parent());
        $parent->remove_topic();
        $parent->remove_post($lo->get_total_posts());
        
        $parent->recalculate_last_post();
        
        return true;
    }

    function get_allowed_types()
    {
        return array(ForumPost :: get_type_name());
    }
}
?>
<?php
/**
 * $Id: complex_forum.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum
 */
class ComplexForum extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array(Forum :: get_type_name(), ForumTopic :: get_type_name());
    }

    function create()
    {
        parent :: create();
        
        $rdm = RepositoryDataManager :: get_instance();
        $lo = $rdm->retrieve_content_object($this->get_ref());
        
        $parent = $rdm->retrieve_content_object($this->get_parent());
        $parent->add_topic($lo->get_total_topics());
        $parent->add_post($lo->get_total_posts());
        $parent->recalculate_last_post($this->get_ref());
    }

    function delete()
    {
        $succes = parent :: delete();
        
        $rdm = RepositoryDataManager :: get_instance();
        $lo = $rdm->retrieve_content_object($this->get_ref());
        
        $parent = $rdm->retrieve_content_object($this->get_parent());
        $parent->remove_topic($lo->get_total_topics());
        $parent->remove_post($lo->get_total_posts());  
        $parent->recalculate_last_post();
        
        return $succes;
    }
}
?>
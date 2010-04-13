<?php
/**
 * $Id: feedback_manager_component.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.feedback_manager
 */

/**
 * Description of feedback_manager_componentclass
 *
 * @author pieter
 */
class FeedbackManagerComponent extends SubManagerComponent
{
	function get_application()
    {
        return $this->get_parent()->get_application();
    }
    
    function set_application($application)
    {
    	return $this->get_parent()->get_application();
    }
    
	function get_publication_id()
    {
        return $this->get_parent()->get_publication_id();
    }
    
    function set_publication_id($publication_id)
    {
    	return $this->get_parent()->set_publication_id($publication_id);
    }
    
	function get_complex_wrapper_id()
    {
        return $this->get_parent()->get_complex_wrapper_id();
    }
    
    function set_complex_wrapper_id($complex_wrapper_id)
    {
    	return $this->get_parent()->set_complex_wrapper_id($complex_wrapper_id);
    }
    
    function set_default_content_object($type, $content_object)
    {
        return $this->get_parent()->get_application();
    }

    /**
     * @see ObjectPublisher::get_default_object()
     */
    function get_default_content_object($type)
    {
        return $this->get_parent()->get_default_content_object($type);
    }

    function retrieve_feedback_publications($pid, $cid, $application)
    {
        return $this->get_parent()->retrieve_feedback_publications($pid, $cid, $application);
    }
    
    function add_actionbar_item($link)
    {
        $this->get_parent()->add_to_action_bar($link);
    }

    function retrieve_feedback_publication($id)
    {
        return $this->get_parent()->retrieve_feedback_publication($id);
    }
}
?>
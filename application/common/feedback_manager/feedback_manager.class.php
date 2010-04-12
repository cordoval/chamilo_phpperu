<?php
/**
 * $Id: feedback_manager.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.feedback_manager
 */

/**
 * Description of feeback_manager
 *
 * @author pieter
 */

require_once dirname(__FILE__) . '/feedback_manager_component.class.php';

class FeedbackManager extends SubManager
{
    
    const PARAM_ACTION = 'feedback_action';
    const PARAM_FEEDBACK_ID = 'feedback_id';
    
    const PARAM_REMOVE_FEEDBACK = 'remove_feedback';
    const ACTION_BROWSE_FEEDBACK = 'browse_feedback';
    const ACTION_CREATE_FEEDBACK = 'create_feedback';
    const ACTION_UPDATE_FEEDBACK = 'update_feedback';
    const ACTION_DELETE_FEEDBACK = 'delete_feedback';
    
    private $parameters;
    private $application;
    private $publication_id;
    private $complex_wrapper_id;

    function FeedbackManager($parent, $application, $publication_id, $complex_wrapper_id)
    {
        parent :: __construct($parent);
        
        $this->application = $application;
        $this->publication_id = $publication_id;
        $this->complex_wrapper_id = $complex_wrapper_id;
        
    	$action = Request :: get(self :: PARAM_ACTION);
        if ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }
    }

    function run()
    {
        return $this->get_component()->run();
    }

    function as_html()
    {
        return $this->get_component()->as_html();
    }
    
    function get_component()
    {
    	$action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_BROWSE_FEEDBACK :
                $component = FeedbackManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_CREATE_FEEDBACK :
                
                $component = FeedbackManagerComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_UPDATE_FEEDBACK :
                $component = FeedbackManagerComponent :: factory('Updater', $this);
                break;
            case self :: ACTION_DELETE_FEEDBACK :
                $component = FeedbackManagerComponent :: factory('Deleter', $this);
                break;
            default :
                $component = FeedbackManagerComponent :: factory('Browser', $this);
        }
        
        return $component;
    }

    // General functions
    
    /**
     * Returns the action that the user selected.
     * @return string The action.
     */
    function get_action()
    {
        return $this->get_parameter(self :: PARAM_ACTION);
    }
    
	function get_application_component_path() 
	{
		return dirname(__FILE__) . '/component/';		
	}

	// Getters and setters for values
    
    function get_application()
    {
        return $this->application;
    }
    
    function set_application($application)
    {
    	$this->application = $application;
    }
    
	function get_publication_id()
    {
        return $this->publication_id;
    }
    
    function set_publication_id($publication_id)
    {
    	$this->publication_id = $publication_id;
    }
    
	function get_complex_wrapper_id()
    {
        return $this->complex_wrapper_id;
    }
    
    function set_complex_wrapper_id($complex_wrapper_id)
    {
    	$this->complex_wrapper_id = $complex_wrapper_id;
    }	

	// Data retrieval functions
	
	function retrieve_feedback_publications($pid, $cid, $application)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->retrieve_feedback_publications($pid, $cid, $application);
    }    

    function retrieve_feedback_publication($id)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->retrieve_feedback_publication($id);
    }
    
    // Additional methods
    
    /**
     * Sets a default learning object. When the creator component of this
     * publisher is displayed, the properties of the given learning object will
     * be used as the default form values.
     * @param string $type The learning object type.
     * @param ContentObject $content_object The learning object to use as the
     *                                        default for the given type.
     */
    function set_default_content_object($type, $content_object)
    {
        $this->default_content_objects[$type] = $content_object;
    }

    function get_default_content_object($type)
    {
        if (isset($this->default_content_objects[$type]))
        {
            return $this->default_content_objects[$type];
        }
        return new AbstractContentObject($type, $this->get_user_id());
    }

    function add_actionbar_item($link)
    {
        $this->get_parent()->add_actionbar_item($link);
    }

}
?>
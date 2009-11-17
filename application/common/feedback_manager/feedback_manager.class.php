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

class FeedbackManager
{
    
    const PARAM_ACTION = 'feedback_action';
    const PARAM_FEEDBACK_ID = 'feedback_id';
    const PARAM_REMOVE_FEEDBACK = 'remove_feedback';
    const ACTION_BROWSE_FEEDBACK = 'browse_feedback';
    const ACTION_CREATE_FEEDBACK = 'create_feedback';
    const ACTION_UPDATE_FEEDBACK = 'update_feedback';
    const ACTION_DELETE_FEEDBACK = 'delete_feedback';
    
    private $parent;
    
    private $parameters;
    
    private $application;

    function FeedbackManager($parent, $application)
    {
        $this->parent = $parent;
        $this->application = $application;
        //$parent->set_parameter(self :: PARAM_ACTION, $this->get_action());
    //$this->parse_input_from_table();
    }

    function run()
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
        $component->run();
    
    }

    function as_html()
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
        return $component->as_html();
    }

    /**
     * Returns the tool which created this publisher.
     * @return Tool The tool.
     */
    function get_parent()
    {
        return $this->parent;
    }

    function display_header($breadcrumbtrail)
    {
        return $this->parent->display_header($breadcrumbtrail, false, false);
    }

    function display_footer()
    {
        return $this->parent->display_footer();
    }

    /**
     * @see Tool::get_user_id()
     */
    function get_user_id()
    {
        return $this->parent->get_user_id();
    }

    function get_user()
    {
        return $this->parent->get_user();
    }

    function get_application()
    {
        return $this->application;
    }

    /**
     * Returns the action that the user selected, or "publicationcreator" if none.
     * @return string The action.
     */
    function get_action()
    {
        return $_GET[self :: PARAM_ACTION];
    }

    function get_url($parameters = array(), $encode = false)
    {
        return $this->parent->get_url($parameters, $encode);
    }

    function get_parameters()
    {
        return $this->parent->get_parameters();
    }

    function set_parameter($name, $value)
    {
        $this->parent->set_parameter($name, $value);
    }

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

    function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
    {
        return $this->parent->redirect($action, $message, $error_message, $extra_params);
    }

    function repository_redirect($action = null, $message = null, $cat_id = 0, $error_message = false, $extra_params = array())
    {
        return $this->parent->redirect($action, $message, $cat_id, $error_message, $extra_params);
    }

    function get_extra_parameters()
    {
        return $this->parameters;
    }

    function set_extra_parameters($parameters)
    {
        $this->parameters = $parameters;
    }

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

    function add_actionbar_item($link)
    {
        $this->parent->add_actionbar_item($link);
    }

}
?>

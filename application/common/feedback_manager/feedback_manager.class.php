<?php
/**
 * $Id: feedback_manager.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.feedback_manager
 */

/**
 * Description of feedback_manager
 *
 * @author pieter
 */

class FeedbackManager extends SubManager
{

    const PARAM_ACTION = 'feedback_action';
    const PARAM_FEEDBACK_ID = 'feedback_id';

    const PARAM_REMOVE_FEEDBACK = 'remove_feedback';
    const ACTION_BROWSE_FEEDBACK = 'browse_feedback';
    const ACTION_CREATE_FEEDBACK = 'create_feedback';
    const ACTION_UPDATE_FEEDBACK = 'update_feedback';
    const ACTION_DELETE_FEEDBACK = 'delete_feedback';

    const ACTION_BROWSE_ONLY_FEEDBACK = 'browse_only_feedback';
    const ACTION_CREATE_ONLY_FEEDBACK = 'create_only_feedback';

    private $parameters;
    private $application;
    private $publication_id;
    private $complex_wrapper_id;

    function FeedbackManager($parent, $application, $publication_id, $complex_wrapper_id, $optional_action = null)
    {
        parent :: __construct($parent);

        $this->application = $application;
        $this->publication_id = $publication_id;
        $this->complex_wrapper_id = $complex_wrapper_id;

        $action = Request :: get(self :: PARAM_ACTION);

        if ($optional_action != null && $action != FeedbackManager :: ACTION_DELETE_FEEDBACK && $action != FeedbackManager :: ACTION_UPDATE_FEEDBACK)
        {
            $this->set_parameter(self :: PARAM_ACTION, $optional_action);
        }
        elseif ($action)
        {
            $this->set_parameter(self :: PARAM_ACTION, $action);
        }

    }

    function run()
    {
        return $this->get_component()->run();
    }

    function get_component()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_BROWSE_FEEDBACK :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_BROWSE_ONLY_FEEDBACK :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_CREATE_ONLY_FEEDBACK :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_CREATE_FEEDBACK :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_UPDATE_FEEDBACK :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE_FEEDBACK :
                $component = $this->create_component('Deleter');
                break;
            default :
                $component = $this->create_component('Browser');
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


    function add_actionbar_item($link)
    {
        $this->get_parent()->add_actionbar_item($link);
    }

    function create_component($type, $application = null)
    {
        $component = parent :: create_component($type, $application);

        if (is_subclass_of($component, __CLASS__))
        {
            $component->set_application($this->get_application());
            $component->set_complex_wrapper_id($this->get_complex_wrapper_id());
            $component->set_publication_id($this->get_publication_id());
        }

        return $component;
    }

}
?>
<?php
/**
 * $Id: publication_manager.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager
 * @author Hans De Bisschop
 */
//require_once dirname(__FILE__) . '/component/registration_browser/registration_browser_table.class.php';


class PhrasesPublicationManager extends SubManager
{
    const PARAM_PUBLICATION_MANAGER_ACTION = 'action';
    const PARAM_PHRASES_PUBLICATION_ID = 'phrase_publication';
    
    const ACTION_BROWSE = 'browser';
    const ACTION_PUBLISH = 'publisher';
    const ACTION_UPDATE = 'updater';
    const ACTION_DELETE = 'deleter';
    const ACTION_VIEW = 'viewer';
    const ACTION_BUILD = 'builder';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    function PhrasesPublicationManager($phrases_manager)
    {
        parent :: __construct($phrases_manager);
        
        $publication_action = Request :: get(self :: PARAM_PUBLICATION_MANAGER_ACTION);
        if ($publication_action)
        {
            $this->set_action($publication_action);
        }
    }

    function set_action($action)
    {
        $this->set_parameter(self :: PARAM_PUBLICATION_MANAGER_ACTION, $action);
    }

    function get_action()
    {
        return $this->get_parameter(self :: PARAM_PUBLICATION_MANAGER_ACTION);
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function retrieve_phrases_publications($condition = null, $offset = 0, $max_objects = -1, $order_by = array ())
    {
        return $this->get_parent()->retrieve_phrases_publications($condition, $order_by, $offset, $max_objects);
    }

    function retrieve_phrases_publication($id)
    {
        return $this->get_parent()->retrieve_phrases_publication($id);
    }

    function count_phrases_publications($condition = null)
    {
        return $this->get_parent()->count_phrases_publications($condition);
    }

    function get_delete_phrases_publication_url($phrases_publication)
    {
        return $this->get_parent()->get_url(array(self :: PARAM_PUBLICATION_MANAGER_ACTION => self :: ACTION_DELETE, self :: PARAM_PHRASES_PUBLICATION_ID => $phrases_publication->get_id()));
    }

    function get_update_phrases_publication_url($phrases_publication)
    {
        return $this->get_parent()->get_url(array(self :: PARAM_PUBLICATION_MANAGER_ACTION => self :: ACTION_UPDATE, self :: PARAM_PHRASES_PUBLICATION_ID => $phrases_publication->get_id()));
    }

    function get_build_phrases_publication_url($phrases_publication)
    {
        return $this->get_parent()->get_url(array(self :: PARAM_PUBLICATION_MANAGER_ACTION => self :: ACTION_BUILD, self :: PARAM_PHRASES_PUBLICATION_ID => $phrases_publication->get_id()));
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_PUBLICATION_MANAGER_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }
}
?>
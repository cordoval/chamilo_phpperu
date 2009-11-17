<?php
/**
 * $Id: linker_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.linker.linker_manager
 */
require_once dirname(__FILE__) . '/linker_manager_component.class.php';
require_once dirname(__FILE__) . '/../linker_data_manager.class.php';

/**
 * A linker manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
class LinkerManager extends WebApplication
{
    const APPLICATION_NAME = 'linker';
    
    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_LINK_ID = 'profile';
    
    const ACTION_DELETE_LINK = 'delete';
    const ACTION_EDIT_LINK = 'edit';
    const ACTION_CREATE_LINK = 'create';
    const ACTION_BROWSE_LINKS = 'browse';
    
    private $parameters;
    private $user;

    /**
     * Constructor
     * @param User $user The current user
     */
    function LinkerManager($user = null)
    {
        $this->user = $user;
        $this->parameters = array();
        $this->set_action(Request :: get(self :: PARAM_ACTION));
    }

    /**
     * Run this linker manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_BROWSE_LINKS :
                $component = LinkerManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_DELETE_LINK :
                $component = LinkerManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_EDIT_LINK :
                $component = LinkerManagerComponent :: factory('Updater', $this);
                break;
            case self :: ACTION_CREATE_LINK :
                $component = LinkerManagerComponent :: factory('Creator', $this);
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_LINKS);
                $component = LinkerManagerComponent :: factory('Browser', $this);
        }
        $component->run();
    }

    // Data Retrieving
    

    function count_links($condition)
    {
        return LinkerDataManager :: get_instance()->count_links($condition);
    }

    function retrieve_links($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return LinkerDataManager :: get_instance()->retrieve_links($condition, $offset, $count, $order_property);
    }

    function retrieve_link($id)
    {
        return LinkerDataManager :: get_instance()->retrieve_link($id);
    }

    // Url Creation
    

    function get_create_link_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_LINK));
    }

    function get_update_link_url($link)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_LINK, self :: PARAM_LINK_ID => $link->get_id()));
    }

    function get_delete_link_url($link)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_LINK, self :: PARAM_LINK_ID => $link->get_id()));
    }

    // Dummy Methods which are needed because we don't work with learning objects
    function content_object_is_published($object_id)
    {
    }

    function any_content_object_is_published($object_ids)
    {
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
    }

    function get_content_object_publication_attribute($object_id)
    {
    
    }

    function count_publication_attributes($type = null, $condition = null)
    {
    
    }

    function delete_content_object_publications($object_id)
    {
    
    }

    function update_content_object_publication_id($publication_attr)
    {
    
    }

    function get_content_object_publication_locations($content_object)
    {
    
    }

    function publish_content_object($content_object, $location)
    {
    
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }
}
?>
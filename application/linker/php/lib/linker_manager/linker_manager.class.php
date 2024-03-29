<?php
namespace application\linker;

use common\libraries\WebApplication;
use common\libraries\Request;
/**
 * $Id: linker_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.linker.linker_manager
 */

/**
 * A linker manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
class LinkerManager extends WebApplication
{
    const APPLICATION_NAME = 'linker';

    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_LINK_ID = 'profile';

    const ACTION_DELETE_LINK = 'deleter';
    const ACTION_EDIT_LINK = 'updater';
    const ACTION_CREATE_LINK = 'creator';
    const ACTION_BROWSE_LINKS = 'browser';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_LINKS;

    private $parameters;
    private $user;

    /**
     * Constructor
     * @param User $user The current user
     */
    function __construct($user = null)
    {
        $this->user = $user;
        $this->parameters = array();
        $this->set_action(Request :: get(self :: PARAM_ACTION));
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

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
}
?>
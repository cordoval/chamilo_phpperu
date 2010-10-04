<?php
/**
 * $Id: webconferencing_manager.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.webconferencing_manager
 */
require_once dirname(__FILE__) . '/../webconferencing_data_manager.class.php';
require_once dirname(__FILE__) . '/component/webconference_browser/webconference_browser_table.class.php';

/**
 * A webconferencing manager
 * @author Stefaan Vanbillemont
 */
class WebconferencingManager extends WebApplication
{
    const APPLICATION_NAME = 'webconferencing';

    const PARAM_WEBCONFERENCE = 'webconference';
    const PARAM_DELETE_SELECTED_WEBCONFERENCES = 'delete_selected_webconferences';

    const ACTION_DELETE_WEBCONFERENCE = 'webconference_deleter';
    const ACTION_EDIT_WEBCONFERENCE = 'webconference_updater';
    const ACTION_CREATE_WEBCONFERENCE = 'webconference_creator';
    const ACTION_BROWSE_WEBCONFERENCES = 'webconferences_browser';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_WEBCONFERENCES;

    /**
     * Constructor
     * @param User $user The current user
     */
    function WebconferencingManager($user = null)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
    }

    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            switch ($_POST['action'])
            {
                case self :: PARAM_DELETE_SELECTED_WEBCONFERENCES :

                    $selected_ids = $_POST[WebconferenceBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

                    if (empty($selected_ids))
                    {
                        $selected_ids = array();
                    }
                    elseif (! is_array($selected_ids))
                    {
                        $selected_ids = array($selected_ids);
                    }

                    $this->set_action(self :: ACTION_DELETE_WEBCONFERENCE);
                    $_GET[self :: PARAM_WEBCONFERENCE] = $selected_ids;
                    break;
            }

        }
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Data Retrieving


    function count_webconferences($condition)
    {
        return WebconferencingDataManager :: get_instance()->count_webconferences($condition);
    }

    function retrieve_webconferences($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return WebconferencingDataManager :: get_instance()->retrieve_webconferences($condition, $offset, $count, $order_property);
    }

    function retrieve_webconference($id)
    {
        return WebconferencingDataManager :: get_instance()->retrieve_webconference($id);
    }

    function count_webconference_options($condition)
    {
        return WebconferencingDataManager :: get_instance()->count_webconference_options($condition);
    }

    function retrieve_webconference_options($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return WebconferencingDataManager :: get_instance()->retrieve_webconference_options($condition, $offset, $count, $order_property);
    }

    function retrieve_webconference_option($id)
    {
        return WebconferencingDataManager :: get_instance()->retrieve_webconference_option($id);
    }

    // Url Creation


    function get_create_webconference_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_WEBCONFERENCE));
    }

    function get_update_webconference_url($webconference)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_WEBCONFERENCE, self :: PARAM_WEBCONFERENCE => $webconference->get_id()));
    }

    function get_delete_webconference_url($webconference)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_WEBCONFERENCE, self :: PARAM_WEBCONFERENCE => $webconference->get_id()));
    }

    function get_browse_webconferences_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_WEBCONFERENCES));
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
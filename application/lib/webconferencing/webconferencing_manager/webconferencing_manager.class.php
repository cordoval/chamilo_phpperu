<?php
/**
 * $Id: webconferencing_manager.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.webconferencing_manager
 */
require_once dirname(__FILE__) . '/webconferencing_manager_component.class.php';
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
    
    const ACTION_DELETE_WEBCONFERENCE = 'delete_webconference';
    const ACTION_EDIT_WEBCONFERENCE = 'edit_webconference';
    const ACTION_CREATE_WEBCONFERENCE = 'create_webconference';
    const ACTION_BROWSE_WEBCONFERENCES = 'browse_webconferences';

    /**
     * Constructor
     * @param User $user The current user
     */
    function WebconferencingManager($user = null)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
    }

    /**
     * Run this webconferencing manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_BROWSE_WEBCONFERENCES :
                $component = WebconferencingManagerComponent :: factory('WebconferencesBrowser', $this);
                break;
            case self :: ACTION_DELETE_WEBCONFERENCE :
                $component = WebconferencingManagerComponent :: factory('WebconferenceDeleter', $this);
                break;
            case self :: ACTION_EDIT_WEBCONFERENCE :
                $component = WebconferencingManagerComponent :: factory('WebconferenceUpdater', $this);
                break;
            case self :: ACTION_CREATE_WEBCONFERENCE :
                $component = WebconferencingManagerComponent :: factory('WebconferenceCreator', $this);
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_WEBCONFERENCES);
                $component = WebconferencingManagerComponent :: factory('WebconferencesBrowser', $this);
        
        }
        $component->run();
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
    
	function delete_content_object_publication($publication_id)
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
}
?>
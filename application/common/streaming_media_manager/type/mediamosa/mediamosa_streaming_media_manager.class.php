<?php

/**
 * Description of mediamosa_streaming_media_manager
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/mediamosa_streaming_media_object.class.php';
require_once dirname(__FILE__) . '/mediamosa_streaming_media_connector.class.php';
require_once dirname(__FILE__) . '/mediamosa_streaming_media_server_object.class.php';
require_once dirname(__FILE__) . '/forms/mediamosa_streaming_media_manager_server_select_form.class.php';
require_once dirname(__FILE__) . '/mediamosa_streaming_media_data_manager.class.php';

class MediamosaStreamingMediaManager extends StreamingMediaManager{

    const ACTION_MANAGE_SETTINGS = 'settings';
    const ACTION_CLEAN_STREAMING_MEDIA = 'clean';
    const ACTION_ADD_SETTING = 'add_setting';
    const ACTION_UPDATE_SETTING = 'update_setting';
    const ACTION_DELETE_SETTING = 'delete_setting';

    const PARAM_MEDIAFILE = 'mediafile_id';
    const PARAM_SERVER = 'server_id';
    const PARAM_STREAMING_MEDIA_SETTING_ID = 'setting_id';

    private static $server;
    private $server_selection_form;

    function MediamosaStreamingVideoManager($application)
    {
        parent :: __construct($application);
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'streaming_media_manager/type/mediamosa/component/';
    }

    function count_streaming_media_objects($condition)
    {

    }

    function retrieve_streaming_media_server_object($id)
    {
        if(self :: $server)
        {
            if(self :: $server->get_id() == $id)
            {
                return self :: $server;
            }
        }
        $dm = MediamosaStreamingMediaDataManager :: get_instance();
        self :: $server = $dm->retrieve_streaming_media_server_object($id);
        return self :: $server;
    }

    function retrieve_streaming_media_objects($condition, $order_property, $offset, $count)
    {
        $connector = MediamosaStreamingMediaConnector::get_instance();
        return $connector->retrieve_mediamosa_assets($condition, $order_property, $offset, $count);
    }

    function retrieve_streaming_media_asset($asset_id)
    {
        $connector = MediamosaStreamingMediaConnector::get_instance();
        return $connector->retrieve_mediamosa_asset($id);
    }

    function translate_search_query($query)
    {}

    function get_menu_items(){}

    function get_streaming_media_object_viewing_url($object){
        $parameters = array();
        $parameters[self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = self :: ACTION_VIEW_STREAMING_MEDIA;
        $parameters[self :: PARAM_STREAMING_MEDIA_ID] = $object->get_id();	

        return $this->get_url($parameters);
    }

    function retrieve_streaming_media_object($id)
    {
        $connector = MediamosaStreamingMediaConnector::get_instance();
        return $connector->retrieve_mediamosa_asset($id);
    }

    function delete_streaming_media_object($id){
        $connector = MediamosaStreamingMediaConnector :: get_instance();
        return $connector->remove_mediamosa_asset($id);
    }

    function export_streaming_media_object($id){}

    function is_editable($id)
    {
        $connector = MediamosaStreamingMediaConnector :: get_instance();
    	return $connector->is_editable($id);
    }

    /*function is_standalone()
    {}*/

    function run()
    {
        $server = Request :: get(self :: PARAM_SERVER);
        if ($server)
        {
            $this->set_parameter(self :: PARAM_SERVER, $server);
        }
        $parent = $this->get_parameter(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION);

        switch ($parent)
        {
            case parent :: ACTION_BROWSE_STREAMING_MEDIA :
                $component = $this->create_component('Browser', $this);
                break;
            case parent :: ACTION_UPLOAD_STREAMING_MEDIA :
                $component = $this->create_component('Uploader', $this);
                break;
            case parent :: ACTION_VIEW_STREAMING_MEDIA :
                $component = $this->create_component('Viewer', $this);
                break;
            case parent :: ACTION_DELETE_STREAMING_MEDIA :
                $component = $this->create_component('Deleter', $this);
                break;
            case parent :: ACTION_EDIT_STREAMING_MEDIA :
                $component = $this->create_component('Editor', $this);
                break;
            case parent :: ACTION_SELECT_STREAMING_MEDIA :
                $component = $this->create_component('Selecter', $this);
                break;
            case self :: ACTION_CLEAN_STREAMING_MEDIA :
                $component = $this->create_component('Cleaner', $this);
                break;
            case self::ACTION_MANAGE_SETTINGS :
                $component = $this->create_component('SettingsManager');
                break;
            case self::ACTION_ADD_SETTING :
                $component = $this->create_component('SettingCreator');
                break;
            case self::ACTION_UPDATE_SETTING :
                $component = $this->create_component('SettingUpdater');
                break;
            case self::ACTION_DELETE_SETTING :
                $component = $this->create_component('SettingDeleter');
                break;
            case self::ACTION_IMPORT_STREAMING_MEDIA :
                $component = $this->create_component('Importer');
                break;
            default :
                $component = $this->create_component('Browser', $this);
                $this->set_parameter(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION, StreamingMediaManager :: ACTION_BROWSE_STREAMING_MEDIA);
                
        }

        $component->run();
    }

    function get_streaming_media_actions()
    {
            //return array(parent :: ACTION_BROWSE_STREAMING_MEDIA, parent :: ACTION_UPLOAD_STREAMING_MEDIA, self :: ACTION_CLEAN_STREAMING_MEDIA);
        $actions = array();
        
        $actions[] = parent :: ACTION_BROWSE_STREAMING_MEDIA;
        
        $actions[] = parent :: ACTION_UPLOAD_STREAMING_MEDIA;
        
        if($this->get_user()->is_platform_admin()) $actions[] = self :: ACTION_MANAGE_SETTINGS;

        return $actions;
    }

    /**
     * Gets the available links to display in the platform admin
     * @retun array of links and actions
     */
    function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = array('name' => Translation :: get('SetMediamosaDefaults'), 'action' => 'category', 'url' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_SET_MEDIAMOSA_DEFAULTS)));

        $info = parent :: get_application_platform_admin_links();
        $info['links'] = $links;
        return $info;
    }

    function display_header()
    {
        parent :: display_header();

        if($this->server_selection_form) $this->server_selection_form->display();
    }

    function set_server_selection_form($form)
    {
        $this->server_selection_form = $form;
    }

    function get_server_selection_form()
    {
        return $this->server_selection_form;
    }

    function import_streaming_media_object($object)
    {
        $streaming_video_clip = new StreamingVideoClip();

        $streaming_video_clip->set_title($object->get_title());
        $streaming_video_clip->set_description($object->get_description());
        $streaming_video_clip->set_asset_id($object->get_id());
        $streaming_video_clip->set_server_id(Request :: get(self :: PARAM_SERVER));
        $streaming_video_clip->set_owner_id($this->get_user_id());
        return $streaming_video_clip->create();
    }
}
?>

<?php

/**
 * Description of mediamosa_streaming_media_manager
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/mediamosa_streaming_media_object.class.php';
require_once dirname(__FILE__) . '/mediamosa_streaming_media_connector.class.php';

class MediamosaStreamingMediaManager extends StreamingMediaManager{

    const ACTION_SET_MEDIAMOSA_SETTINGS = 'settings';
    const ACTION_CLEAN_STREAMING_MEDIA = 'clean';

    const PARAM_MEDIAFILE = 'mediafile_id';

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

    function retrieve_streaming_media_objects($condition, $order_property, $offset, $count)
    {
        $connector = MediamosaStreamingMediaConnector::get_instance($this);
        return $connector->retrieve_mediamosa_assets($condition, $order_property, $offset, $count);
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
        $connector = MediamosaStreamingMediaConnector::get_instance($this);
        return $connector->retrieve_mediamosa_asset($id);
    }

    function delete_streaming_media_object($id){
        $connector = MediamosaStreamingMediaConnector :: get_instance($this);
        return $connector->remove_mediamosa_asset($id);
    }

    function export_streaming_media_object($id){}

    function is_editable($id)
    {
        $connector = MediamosaStreamingMediaConnector :: get_instance($this);
    	return $connector->is_editable($id);
    }

    /*function is_standalone()
    {}*/

    function run()
    {
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
            case self::ACTION_SET_MEDIAMOSA_SETTINGS :
                $component = $this->create_component('SettingsCreator', $application);

            default :
                $component = $this->create_component('Browser', $this);
                $this->set_parameter(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION, StreamingMediaManager :: ACTION_BROWSE_STREAMING_MEDIA);
                break;
        }

        $component->run();
    }

    function get_streaming_media_actions()
    {
            //return array(parent :: ACTION_BROWSE_STREAMING_MEDIA, parent :: ACTION_UPLOAD_STREAMING_MEDIA, self :: ACTION_CLEAN_STREAMING_MEDIA);
        return array(parent :: ACTION_BROWSE_STREAMING_MEDIA, parent :: ACTION_UPLOAD_STREAMING_MEDIA, self :: ACTION_SET_MEDIAMOSA_SETTINGS);
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

}
?>

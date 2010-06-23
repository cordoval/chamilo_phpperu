<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */

/*require_once dirname(__FILE__) . '/settings_manager/settings_manager_cell_renderer.class.php';
require_once dirname(__FILE__) . '/settings_manager/settings_manager_table_column_model.class.php';
require_once dirname(__FILE__) . '/settings_manager/settings_manager_table_data_provider.class.php';*/
require_once dirname(__FILE__) . '/../mediamosa_streaming_media_server_object.class.php';
require_once dirname(__FILE__) . '/settings_manager/settings_manager_table.class.php';
require_once dirname(__FILE__) . '/../mediamosa_streaming_media_data_manager.class.php';

class MediamosaStreamingMediaManagerSettingsManagerComponent extends MediamosaStreamingMediaManager {

    function run()
    {
        

        $params = array();

        $table = new SettingsManagerTable($this, $params);

        $this->display_header();

        echo $this->get_general_toolbar();
        echo $table->as_html();

        $this->display_footer();

    }

   

    function get_general_toolbar()
    {
        $toolbar =  new Toolbar();
        //, MediamosaStreamingMediaManager :: PARAM_STREAMING_MEDIA_SETTING_ID => $id
        $toolbar_item_add = new ToolbarItem(Translation :: get('Add server'), Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => MediamosaStreamingMediaManager :: ACTION_ADD_SETTING)));
        $toolbar->add_item($toolbar_item_add);

        return $toolbar->as_html();
    }

    function get_server_viewing_url($server_object)
    {
        $params = array();
        $params[MediamosaStreamingMediaManager :: PARAM_SERVER] = $server_object->get_id();
        $params[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = MediamosaStreamingMediaManager :: ACTION_UPDATE_SETTING;
        return $this->get_url($params);
    }

    function get_server_editing_url($server_object)
    {
        $params = array();
        $params[MediamosaStreamingMediaManager :: PARAM_SERVER] = $server_object->get_id();
        $params[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = MediamosaStreamingMediaManager :: ACTION_UPDATE_SETTING;
        return $this->get_url($params);
    }

    function get_server_recycling_url($server_object)
    {
        $params = array();
        $params[MediamosaStreamingMediaManager :: PARAM_SERVER] = $server_object->get_id();
        $params[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = MediamosaStreamingMediaManager :: ACTION_DELETE_SETTING;
        return $this->get_url($params);
    }
}
?>

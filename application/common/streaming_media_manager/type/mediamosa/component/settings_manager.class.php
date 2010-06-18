<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaManagerSettingsManagerComponent extends MediamosaStreamingMediaManager {

    function run()
    {
        
    }

    function install()
    {
        //create table mediamosa_streaming_media_settings
        //id, url, name, login, password, is_upload_possible
    }

    function get_general_toolbar()
    {
        $toolbar =  new Toolbar();
        //, MediamosaStreamingMediaManager :: PARAM_STREAMING_MEDIA_SETTING_ID => $id
        $toolbaritem_add = new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION => MediamosaStreamingMediaManager :: ACTION_ADD_SETTING)));
        $toolbar->add_item($toolbar_item_edit);

        return $toolbar->as_html();
    }
}
?>

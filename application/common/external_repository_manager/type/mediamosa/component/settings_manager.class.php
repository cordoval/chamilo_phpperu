<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */

/*require_once dirname(__FILE__) . '/settings_manager/settings_manager_cell_renderer.class.php';
require_once dirname(__FILE__) . '/settings_manager/settings_manager_table_column_model.class.php';
require_once dirname(__FILE__) . '/settings_manager/settings_manager_table_data_provider.class.php';*/
require_once dirname(__FILE__) . '/../mediamosa_external_repository_server_object.class.php';
require_once dirname(__FILE__) . '/settings_manager/settings_manager_table.class.php';
require_once dirname(__FILE__) . '/../mediamosa_external_repository_data_manager.class.php';

class MediamosaExternalRepositoryManagerSettingsManagerComponent extends MediamosaExternalRepositoryManager {

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
        //, MediamosaExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_SETTING_ID => $id
        $toolbar_item_add = new ToolbarItem(Translation :: get('Add server'), Theme :: get_common_image_path() . 'action_add.png', $this->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => MediamosaExternalRepositoryManager :: ACTION_ADD_SETTING)));
        $toolbar->add_item($toolbar_item_add);

        return $toolbar->as_html();
    }

    function get_server_viewing_url($server_object)
    {
        $params = array();
        $params[MediamosaExternalRepositoryManager :: PARAM_SERVER] = $server_object->get_id();
        $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = MediamosaExternalRepositoryManager :: ACTION_UPDATE_SETTING;
        return $this->get_url($params);
    }

    function get_server_editing_url($server_object)
    {
        $params = array();
        $params[MediamosaExternalRepositoryManager :: PARAM_SERVER] = $server_object->get_id();
        $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = MediamosaExternalRepositoryManager :: ACTION_UPDATE_SETTING;
        return $this->get_url($params);
    }

    function get_server_recycling_url($server_object)
    {
        $params = array();
        $params[MediamosaExternalRepositoryManager :: PARAM_SERVER] = $server_object->get_id();
        $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = MediamosaExternalRepositoryManager :: ACTION_DELETE_SETTING;
        return $this->get_url($params);
    }
}
?>

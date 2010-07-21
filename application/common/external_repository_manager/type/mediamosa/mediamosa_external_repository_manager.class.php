<?php

/**
 * Description of mediamosa_external_repository_manager
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/mediamosa_external_repository_object.class.php';
require_once dirname(__FILE__) . '/mediamosa_external_repository_connector.class.php';
require_once dirname(__FILE__) . '/mediamosa_external_repository_server_object.class.php';
require_once dirname(__FILE__) . '/mediamosa_external_repository_user_quotum.class.php';
require_once dirname(__FILE__) . '/forms/mediamosa_external_repository_manager_server_select_form.class.php';
require_once dirname(__FILE__) . '/mediamosa_external_repository_data_manager.class.php';

class MediamosaExternalRepositoryManager extends ExternalRepositoryManager
{
    
    const REPOSITORY_TYPE = 'mediamosa';
    
    const ACTION_MANAGE_SETTINGS = 'settings';
    const ACTION_CLEAN_EXTERNAL_REPOSITORY = 'clean';
    const ACTION_ADD_SETTING = 'add_setting';
    const ACTION_UPDATE_SETTING = 'update_setting';
    const ACTION_DELETE_SETTING = 'delete_setting';
    
    const PARAM_MEDIAFILE = 'mediafile_id';
    const PARAM_SERVER = 'server_id';
    const PARAM_EXTERNAL_REPOSITORY_SETTING_ID = 'setting_id';
    
    private static $server;
    private $server_selection_form;

    function MediamosaStreamingVideoManager($application)
    {
        parent :: __construct($application);
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'external_repository_manager/type/mediamosa/component/';
    }

    /**
     * @return MediamosaExternalRepositoryConnector
     */
    function get_external_repository_connector()
    {
        return MediamosaExternalRepositoryConnector :: get_instance();
    }

    function retrieve_external_repository_server_object($id)
    {
        if (self :: $server)
        {
            if (self :: $server->get_id() == $id)
            {
                return self :: $server;
            }
        }
        $dm = MediamosaExternalRepositoryDataManager :: get_instance($this);
        self :: $server = $dm->retrieve_external_repository_server_object($id);
        return self :: $server;
    }

    function retrieve_external_repository_asset($asset_id)
    {
        return $this->get_external_repository_connector()->retrieve_mediamosa_asset($id);
    }

    function get_menu_items()
    {
        return array();
    }

    function get_external_repository_object_viewing_url(ExternalRepositoryObject $object)
    {
        $parameters = array();
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = self :: ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        
        return ($object->is_usable()) ? $this->get_url($parameters) : '#';
    }

    function run()
    {
        $server = Request :: get(self :: PARAM_SERVER);
        if ($server)
        {
            $this->set_parameter(self :: PARAM_SERVER, $server);
        }
        $parent = $this->get_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION);
        
        switch ($parent)
        {
            case parent :: ACTION_BROWSE_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Browser', $this);
                break;
            case parent :: ACTION_UPLOAD_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Uploader', $this);
                break;
            case parent :: ACTION_VIEW_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Viewer', $this);
                break;
            case parent :: ACTION_DELETE_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Deleter', $this);
                break;
            case parent :: ACTION_EDIT_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Editor', $this);
                break;
            case parent :: ACTION_SELECT_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Selecter', $this);
                break;
            case self :: ACTION_CLEAN_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Cleaner', $this);
                break;
            case self :: ACTION_MANAGE_SETTINGS :
                $component = $this->create_component('SettingsManager');
                break;
            case self :: ACTION_ADD_SETTING :
                $component = $this->create_component('SettingCreator');
                break;
            case self :: ACTION_UPDATE_SETTING :
                $component = $this->create_component('SettingUpdater');
                break;
            case self :: ACTION_DELETE_SETTING :
                $component = $this->create_component('SettingDeleter');
                break;
            case self :: ACTION_IMPORT_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Importer');
                break;
            case ExternalRepositoryManager :: ACTION_CONFIGURE_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Configurer');
                break;
            default :
                $component = $this->create_component('Browser', $this);
                $this->set_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY);
        
        }
        
        $component->run();
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
        
        if ($this->server_selection_form)
            $this->server_selection_form->display();
    }

    function set_server_selection_form($form)
    {
        $this->server_selection_form = $form;
    }

    function get_server_selection_form()
    {
        return $this->server_selection_form;
    }

    function import_external_repository_object($object)
    {
        //        xdebug_break();
        $streaming_video_clip = new StreamingVideoClip();
        
        $streaming_video_clip->set_title($object->get_title());
        $streaming_video_clip->set_description($object->get_description());
        $streaming_video_clip->set_asset_id($object->get_id());
        $streaming_video_clip->set_server_id(Request :: get(self :: PARAM_SERVER));
        $streaming_video_clip->set_publisher($object->get_publisher());
        $streaming_video_clip->set_creator($object->get_creator());
        $streaming_video_clip->set_owner_id($this->get_user_id());
        return $streaming_video_clip->create();
    }

    function create_standard_user_quota($server_id)
    {
        //        $udm = UserDataManager :: get_instance();
        $mdm = MediamosaExternalRepositoryDataManager :: get_instance();
        //
        //        $users = $udm->retrieve_users();
        $mediamosa_server_object = $mdm->retrieve_external_repository_server_object($server_id);
        //        xdebug_break();
        //        while($user = $users->next_result())
        //        {
        $mediamosa_user_quotum = new ExternalRepositoryUserQuotum();
        //
    //            $mediamosa_user_quotum->set_user_id($user->get_id());
    //            $mediamosa_user_quotum->set_server_id($server_id);
    //            $mediamosa_user_quotum->set_quotum($mediamosa_server_object->get_default_user_quotum());
    //
    //            $mdm->create_mediamosa_user_quotum($mediamosa_user_quotum);
    //        }
    }

    function get_available_renderers()
    {
        return array(ExternalRepositoryObjectRenderer :: TYPE_GALLERY, ExternalRepositoryObjectRenderer :: TYPE_SLIDESHOW, ExternalRepositoryObjectRenderer :: TYPE_TABLE);
    }

    function validate_settings()
    {
        $settings = array('url', 'login', 'password');
        
        foreach ($settings as $variable)
        {
            $value = ExternalRepositorySetting :: get($variable);
            if (! $value)
            {
                return false;
            }
        }
        return true;
    }

    function get_content_object_type_conditions()
    {
        $video_types = Document :: get_video_types();
        $video_conditions = array();
        foreach ($video_types as $video_type)
        {
            $video_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.' . $video_type, Document :: get_type_name());
        }
        
        return new OrCondition($video_conditions);
    }

    function get_repository_type()
    {
        return self :: REPOSITORY_TYPE;
    }

    function get_external_repository_actions()
    {
        $actions = array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY, self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY, self :: ACTION_EXPORT_EXTERNAL_REPOSITORY);
        
        $is_platform = $this->get_user()->is_platform_admin() && (count(ExternalRepositorySetting :: get_all()) > 0);
        
        if ($is_platform)
        {
            $actions[] = self :: ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }
        
        return $actions;
    }
}
?>
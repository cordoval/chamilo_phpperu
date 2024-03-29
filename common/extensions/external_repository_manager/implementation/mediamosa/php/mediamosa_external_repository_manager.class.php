<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;

use common\libraries\OrCondition;

use repository\content_object\document\Document;

use repository\content_object\mediamosa\Mediamosa;

use common\libraries\BreadcrumbTrail;

use common\libraries\Request;

use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryObject;
use common\extensions\external_repository_manager\ExternalRepositoryObjectRenderer;
use repository\ExternalSetting;
use common\libraries\Translation;
use common\libraries\ActionBarSearchForm;
use repository\RepositoryDataManager;
use common\libraries\PatternMatchCondition;
use common\libraries\Breadcrumb;
use common\libraries\Path;
use common\libraries\Utilities;

/**
 * Description of mediamosa_external_repository_manager
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/mediamosa_external_repository_object.class.php';
require_once dirname(__FILE__) . '/mediamosa_external_repository_manager_connector.class.php';

class MediamosaExternalRepositoryManager extends ExternalRepositoryManager
{

    const REPOSITORY_TYPE = 'mediamosa';

    const PARAM_MEDIAFILE = 'mediafile_id';
    const PARAM_FEED_TYPE = 'feed';

    const FEED_TYPE_GENERAL = 1;
    const FEED_TYPE_MOST_INTERESTING = 2;
    const FEED_TYPE_MOST_RECENT = 3;
    const FEED_TYPE_MY_VIDEOS = 4;
    const FEED_TYPE_EXTERNAL = 5;
    const FEED_TYPE_ALL = 6;

    const SETTING_SLAVE_APP_IDS = 'slave_app_ids';
    const SETTING_URL = 'url';
    const SETTING_USE_PREFIX = 'use_prefix';

    private static $server;
    private $server_selection_form;

    function MediamosaStreamingVideoManager($external_repository, $application)
    {
        parent :: __construct($external_repository, $application);
    }

    function get_application_component_path()
    {
        return Path :: get_common_extensions_path() . 'external_repository_manager/implementation/mediamosa/php/component/';
    }

    function retrieve_external_repository_asset($asset_id)
    {
        return $this->get_external_repository_manager_connector()->retrieve_mediamosa_asset($id);
    }

    function get_menu_items()
    {
        $general = array();

        $general['title'] = Translation :: get('Browse', null, Utilities :: COMMON_LIBRARIES);
        $general['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_GENERAL), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $general['class'] = 'home';
        $menu_items[] = $general;

        $most_recent =  array();
        $most_recent['title'] = Translation :: get('MostRecent');
        $most_recent['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_MOST_RECENT), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $most_recent['class'] = 'recent';
        $menu_items[] = $most_recent;

        $my_videos = array();
        $my_videos['title'] = Translation :: get('MyVideos');
        $my_videos['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_MY_VIDEOS), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $menu_items[] = $my_videos;

        //display of only external assets is not yet possible
//        $external = array();
//        $external['title'] = Translation :: get('External');
//        $external['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_EXTERNAL), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $menu_items[] = $external;

        if($this->get_user()->is_platform_admin())
        {
            $all_videos = array();
            $all_videos['title'] = Translation :: get('All');
            $all_videos['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_ALL), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
            $menu_items[] = $all_videos;
        }

        return $menu_items;
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
        $rdm = RepositoryDataManager :: get_instance();
        $external_repository = $rdm->retrieve_external_instance(Request :: get(MediamosaExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY));
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb('#', $external_repository->get_title()));

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
//            case self :: ACTION_CLEAN_EXTERNAL_REPOSITORY :
//                $component = $this->create_component('Cleaner', $this);
//                break;
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
        $streaming_video_clip = new Mediamosa();

        $streaming_video_clip->set_title($object->get_title());
        $streaming_video_clip->set_description($object->get_description());
        $streaming_video_clip->set_asset_id($object->get_id());
        $streaming_video_clip->set_server_id(Request :: get(self :: PARAM_SERVER));
        $streaming_video_clip->set_publisher($object->get_publisher());
        $streaming_video_clip->set_creator($object->get_creator());
        $streaming_video_clip->set_owner_id($this->get_user_id());
        return $streaming_video_clip->create();
    }

    function get_available_renderers()
    {
        return array(ExternalRepositoryObjectRenderer :: TYPE_GALLERY, ExternalRepositoryObjectRenderer :: TYPE_SLIDESHOW, ExternalRepositoryObjectRenderer :: TYPE_TABLE);
    }

    function validate_settings($external_repository)
    {
        $settings = array('url', 'loginname', 'password');

        foreach ($settings as $variable)
        {
            $value = ExternalSetting :: get($variable, $external_repository->get_id());
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

        $is_platform = $this->get_user()->is_platform_admin() && (count(ExternalSetting :: get_all($this->get_external_repository()->get_id())) > 0);

        if ($is_platform)
        {
            $actions[] = self :: ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }

        return $actions;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION;
    }
}
?>
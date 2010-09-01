<?php
require_once dirname(__FILE__) . '/matterhorn_external_repository_connector.class.php';
require_once dirname(__FILE__) . '/../../general/streaming/streaming_media_external_repository_browser_gallery_table_cell_renderer.class.php';


/**
 * 
 * @author magali.gillard
 *
 */

class MatterhornExternalRepositoryManager extends ExternalRepositoryManager
{
    const REPOSITORY_TYPE = 'matterhorn';
    
    const PARAM_FEED_TYPE = 'feed';
    const PARAM_FEED_IDENTIFIER = 'identifier';
    
    const FEED_TYPE_GENERAL = 1;
    const FEED_TYPE_FEATURED = 2;
    const FEED_TYPE_NEW_VIDEOS= 3;
    const FEED_TYPE_CATEGORIES = 4;
    const FEED_TYPE_POPULAR = 5;

    const PARAM_MEDIAFILE = 'mediafile_id';

    /**
     * @param Application $application
     */
    function MatterhornExternalRepositoryManager($external_repository, $application)
    {
        parent :: __construct($external_repository, $application);
        $this->set_parameter(self :: PARAM_FEED_TYPE, Request :: get(self :: PARAM_FEED_TYPE));
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_application_component_path()
     */
    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'external_repository_manager/type/matterhorn/component/';
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#validate_settings()
     */
    function validate_settings()
    {
//        $login = ExternalRepositorySetting:: get('login');
//        $password = ExternalRepositorySetting:: get('password');
//
//        if (! $login || ! $password)
//		{
//            return false;
//        }
        return true;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#support_sorting_direction()
     */
    function support_sorting_direction()
    {
        return true;
    }

    /**
     * @param ExternalRepositoryObject $object
     * @return string
     */
    function get_external_repository_object_viewing_url(ExternalRepositoryObject $object)
    {
        $parameters = array();
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = self :: ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();

        return $this->get_url($parameters);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_menu_items()
     */
    function get_menu_items()
    {
        $menu_items = array();
//        $browser = array();
//        $browser['title'] = Translation :: get('Browse');
//        $browser['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_GENERAL), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $browser['class'] = 'home';
//        $menu_items[] = $browser;
//
//        $featured = array();
//        $featured['title'] = Translation :: get('Featured');
//        $featured['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_FEATURED), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $featured['class'] = 'featured';
//        $menu_items[] = $featured;
//        
//        $new_videos = array();
//        $new_videos['title'] = Translation :: get('NewVideos');
//        $new_videos['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_NEW_VIDEOS), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $new_videos['class'] = 'new_video';
//        $menu_items[] = $new_videos;
//
//        $categories = array();
//        $categories['title'] = Translation :: get('Categories');
//        $categories['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_CATEGORIES), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $categories['class'] = 'category';
//        
//        $categorie_items = array();
//		//sub categories
//        $categorie_item = array();
//        $categorie_item['title'] = Translation :: get('AboutOpencast');
//        $categorie_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'about_opencast'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $categorie_item['class'] = 'feed';
//        $categorie_items[] = $categorie_item;
//        
//        $categorie_item = array();
//        $categorie_item['title'] = Translation :: get('OpencastMatterhorn');
//        $categorie_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'opencast_matterhorn'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $categorie_item['class'] = 'feed';
//        $categorie_items[] = $categorie_item;
//        
//        $categorie_item = array();
//        $categorie_item['title'] = Translation :: get('SystemShowcases');
//        $categorie_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'system_showcases'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $categorie_item['class'] = 'feed';
//        $categorie_items[] = $categorie_item;
//        
//        $categorie_item = array();
//        $categorie_item['title'] = Translation :: get('Webcasting');
//        $categorie_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'webcasting'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $categorie_item['class'] = 'feed';
//        $categorie_items[] = $categorie_item;
//        $categories['sub'] = $categorie_items;
//
//        $popular = array();
//        $popular['title'] = Translation :: get('Popular');
//        $popular['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_POPULAR), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $popular['class'] = 'popular';
//        $menu_items[] = $popular;
        
        return $menu_items;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#is_ready_to_be_used()
     */
    function is_ready_to_be_used()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_external_repository_actions()
     */
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

    function run()
    {
        $parent = $this->get_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION);

        switch ($parent)
        {
            case ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Viewer');
                break;
            case ExternalRepositoryManager :: ACTION_EXPORT_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Exporter');
                break;
            case ExternalRepositoryManager :: ACTION_IMPORT_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Importer');
                break;
            case ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Browser', $this);
                break;
            case ExternalRepositoryManager :: ACTION_DOWNLOAD_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Downloader');
                break;
            case ExternalRepositoryManager :: ACTION_UPLOAD_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Uploader');
                break;
            case ExternalRepositoryManager :: ACTION_SELECT_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Selecter');
                break;
            case ExternalRepositoryManager :: ACTION_EDIT_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Editor');
                break;
            case ExternalRepositoryManager :: ACTION_DELETE_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Deleter');
                break;
            case ExternalRepositoryManager :: ACTION_CONFIGURE_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Configurer');
                break;
            case ExternalRepositoryManager :: ACTION_SYNCHRONIZE_EXTERNAL_REPOSITORY :
                $component = $this->create_component('ExternalSyncer');
                break;
            case ExternalRepositoryManager :: ACTION_SYNCHRONIZE_INTERNAL_REPOSITORY :
                $component = $this->create_component('InternalSyncer');
                break;
            default :
                $component = $this->create_component('Browser', $this);
                $this->set_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY);
                break;
        }

        $component->run();
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_available_renderers()
     */
    function get_available_renderers()
    {
        return array(ExternalRepositoryObjectRenderer :: TYPE_GALLERY, ExternalRepositoryObjectRenderer :: TYPE_SLIDESHOW, ExternalRepositoryObjectRenderer :: TYPE_TABLE);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_content_object_type_conditions()
     */
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

    /**
     * @return string
     */
    function get_repository_type()
    {
        return self :: REPOSITORY_TYPE;
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
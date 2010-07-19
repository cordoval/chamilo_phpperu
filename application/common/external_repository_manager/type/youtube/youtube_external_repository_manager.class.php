<?php
require_once dirname(__FILE__) . '/youtube_external_repository_connector.class.php';
require_once dirname(__FILE__) . '/table/youtube_external_repository_browser_gallery_table_property_model.class.php';
require_once dirname(__FILE__) . '/../../general/streaming/streaming_media_external_repository_browser_gallery_table_cell_renderer.class.php';

class YoutubeExternalRepositoryManager extends ExternalRepositoryManager
{
    const REPOSITORY_TYPE = 'youtube';

    const PARAM_FEED_TYPE = 'feed';
    const PARAM_FEED_IDENTIFIER = 'identifier';

    const FEED_TYPE_GENERAL = 1;
    const FEED_TYPE_MYVIDEOS = 2;
    const FEED_STANDARD_TYPE = 3;

    function YoutubeExternalRepositoryManager($application)
    {
        parent :: __construct($application);
        $this->set_parameter(self :: PARAM_FEED_TYPE, Request :: get(self :: PARAM_FEED_TYPE));
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'external_repository_manager/type/youtube/component/';
    }

    function initialize_external_repository(ExternalRepositoryManager $external_repository_manager)
    {
        YoutubeExternalRepositoryConnector :: get_instance($this);
    }

    function validate_settings()
    {
        $developer_key = $this->get_setting('developer_key');

        if (! $developer_key)
        {
            return false;
        }
        return true;
    }

    function count_external_repository_objects($condition)
    {
        $connector = YoutubeExternalRepositoryConnector :: get_instance($this);
        return $connector->count_youtube_video($condition);
    }

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $connector = YoutubeExternalRepositoryConnector :: get_instance($this);
        return $connector->get_youtube_videos($condition, $order_property, $offset, $count);
    }

    function retrieve_external_repository_object($id)
    {
        $connector = YoutubeExternalRepositoryConnector :: get_instance($this);
        return $connector->get_youtube_video($id);
    }

    function delete_external_repository_object($id)
    {
        $connector = YoutubeExternalRepositoryConnector :: get_instance($this);
        return $connector->delete_youtube_video($id);
    }

    function export_external_repository_object($object)
    {
        $connector = YoutubeExternalRepositoryConnector :: get_instance($this);
        return $connector->export_youtube_video($object);
    }

    function support_sorting_direction()
    {
        return false;
    }

    function translate_search_query($query)
    {
        return YoutubeExternalRepositoryConnector :: translate_search_query($query);
    }

    function get_external_repository_object_viewing_url($object)
    {
        $parameters = array();
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = self :: ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();

        return $this->get_url($parameters);
    }

    function is_editable($id)
    {
        $connector = YoutubeExternalRepositoryConnector :: get_instance($this);
        return $connector->is_editable($id);
    }

    function get_menu_items()
    {
        $menu_items = array();
        $browser = array();
        $browser['title'] = Translation :: get('YoutubeBrowse');
        $browser['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_GENERAL), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, self :: PARAM_FEED_IDENTIFIER));
        $browser['class'] = 'home';
        $menu_items[] = $browser;

        $my_videos = array();
        $my_videos['title'] = Translation :: get('MyVideos');
        $my_videos['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_MYVIDEOS), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, self :: PARAM_FEED_IDENTIFIER));
        $my_videos['class'] = 'user';
        $menu_items[] = $my_videos;

        $standard_feeds = array();
        $standard_feeds['title'] = Translation :: get('StandardFeeds');
        $standard_feeds['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, self :: PARAM_FEED_IDENTIFIER));
        $standard_feeds['class'] = 'category';

        $standard_feed_items = array();

        $standard_feed_item = array();
        $standard_feed_item['title'] = Translation :: get('MostViewed');
        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'most_viewed'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $standard_feed_item['class'] = 'feed';
        $standard_feed_items[] = $standard_feed_item;

        $standard_feed_item = array();
        $standard_feed_item['title'] = Translation :: get('TopRated');
        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'top_rated'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $standard_feed_item['class'] = 'feed';
        $standard_feed_items[] = $standard_feed_item;
        $standard_feeds['sub'] = $standard_feed_items;

        $standard_feed_item = array();
        $standard_feed_item['title'] = Translation :: get('RecentlyFeatured');
        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'recently_featured'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $standard_feed_item['class'] = 'feed';
        $standard_feed_items[] = $standard_feed_item;

        $standard_feed_item = array();
        $standard_feed_item['title'] = Translation :: get('WatchOnMobile');
        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'watch_on_mobile'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $standard_feed_item['class'] = 'feed';
        $standard_feed_items[] = $standard_feed_item;

        $standard_feed_item = array();
        $standard_feed_item['title'] = Translation :: get('MostDiscussed');
        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'most_discussed'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $standard_feed_item['class'] = 'feed';
        $standard_feed_items[] = $standard_feed_item;

        $standard_feed_item = array();
        $standard_feed_item['title'] = Translation :: get('TopFavorites');
        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'top_favorites'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $standard_feed_item['class'] = 'feed';
        $standard_feed_items[] = $standard_feed_item;

        $standard_feed_item = array();
        $standard_feed_item['title'] = Translation :: get('MostResponded');
        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'most_responded'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $standard_feed_item['class'] = 'feed';
        $standard_feed_items[] = $standard_feed_item;

        $standard_feed_item = array();
        $standard_feed_item['title'] = Translation :: get('MostRecent');
        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'most_recent'), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $standard_feed_item['class'] = 'feed';
        $standard_feed_items[] = $standard_feed_item;

        $standard_feeds['sub'] = $standard_feed_items;

        $menu_items[] = $standard_feeds;

        return $menu_items;
    }

    function is_ready_to_be_used()
    {
        //        $action = $this->get_parameter(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION);
        //
        //        return self :: any_object_selected() && ($action == self :: ACTION_PUBLISHER);
        return false;
    }

    function get_external_repository_actions()
    {
        $actions = array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY, self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY, self :: ACTION_EXPORT_EXTERNAL_REPOSITORY);

        $is_platform = $this->get_user()->is_platform_admin() && (count($this->get_settings()) > 0);

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
            default :
                $component = $this->create_component('Browser', $this);
                $this->set_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY);
                break;
        }

        $component->run();
    }

    function get_available_renderers()
    {
        return array(ExternalRepositoryObjectRenderer :: TYPE_GALLERY, ExternalRepositoryObjectRenderer :: TYPE_SLIDESHOW, ExternalRepositoryObjectRenderer :: TYPE_TABLE);
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
}
?>
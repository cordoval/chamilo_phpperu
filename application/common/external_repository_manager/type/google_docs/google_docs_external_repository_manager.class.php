<?php
require_once dirname(__FILE__) . '/google_docs_external_repository_connector.class.php';

class GoogleDocsExternalRepositoryManager extends ExternalRepositoryManager
{

    function GoogleDocsExternalRepositoryManager($application)
    {
        parent :: __construct($application);
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'external_repository_manager/type/google_docs/component/';
    }
    
    function initiliaze_external_repository()
    {
        GoogleDocsExternalRepositoryConnector :: get_instance($this);
    }

    function count_external_repository_objects($condition)
    {
        return GoogleDocsExternalRepositoryConnector :: get_instance($this)->count_external_repository_objects($condition);
    }

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        return GoogleDocsExternalRepositoryConnector :: get_instance($this)->retrieve_external_repository_objects($condition, $order_property, $offset, $count);
    }

    function retrieve_external_repository_object($id)
    {
        $connector = GoogleDocsExternalRepositoryConnector :: get_instance($this);
        return $connector->get_youtube_video($id);
    }

    function delete_external_repository_object($id)
    {
        $connector = GoogleDocsExternalRepositoryConnector :: get_instance($this);
        return $connector->delete_youtube_video($id);
    }

    function export_external_repository_object($object)
    {
        $connector = GoogleDocsExternalRepositoryConnector :: get_instance($this);
        return $connector->export_youtube_video($object);
    }

    function support_sorting_direction()
    {
        return false;
    }

    function translate_search_query($query)
    {
        return GoogleDocsExternalRepositoryConnector :: translate_search_query($query);
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
        return false;
//        $connector = GoogleDocsExternalRepositoryConnector :: get_instance($this);
//        return $connector->is_editable($id);
    }

    function get_menu_items()
    {
        $menu_items = array();
//        $browser = array();
//        $browser['title'] = Translation :: get('YoutubeBrowse');
//        $browser['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_GENERAL), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY, self :: PARAM_FEED_IDENTIFIER));
//        $browser['class'] = 'home';
//        $menu_items[] = $browser;
//
//        $my_videos = array();
//        $my_videos['title'] = Translation :: get('MyVideos');
//        $my_videos['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_MYVIDEOS), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY, self :: PARAM_FEED_IDENTIFIER));
//        $my_videos['class'] = 'user';
//        $menu_items[] = $my_videos;
//
//        $standard_feeds = array();
//        $standard_feeds['title'] = Translation :: get('StandardFeeds');
//        $standard_feeds['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY, self :: PARAM_FEED_IDENTIFIER));
//        $standard_feeds['class'] = 'category';
//
//        $standard_feed_items = array();
//
//        $standard_feed_item = array();
//        $standard_feed_item['title'] = Translation :: get('MostViewed');
//        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'most_viewed'), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $standard_feed_item['class'] = 'feed';
//        $standard_feed_items[] = $standard_feed_item;
//
//        $standard_feed_item = array();
//        $standard_feed_item['title'] = Translation :: get('TopRated');
//        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'top_rated'), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $standard_feed_item['class'] = 'feed';
//        $standard_feed_items[] = $standard_feed_item;
//        $standard_feeds['sub'] = $standard_feed_items;
//
//        $standard_feed_item = array();
//        $standard_feed_item['title'] = Translation :: get('RecentlyFeatured');
//        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'recently_featured'), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $standard_feed_item['class'] = 'feed';
//        $standard_feed_items[] = $standard_feed_item;
//
//        $standard_feed_item = array();
//        $standard_feed_item['title'] = Translation :: get('WatchOnMobile');
//        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'watch_on_mobile'), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $standard_feed_item['class'] = 'feed';
//        $standard_feed_items[] = $standard_feed_item;
//
//        $standard_feed_item = array();
//        $standard_feed_item['title'] = Translation :: get('MostDiscussed');
//        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'most_discussed'), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $standard_feed_item['class'] = 'feed';
//        $standard_feed_items[] = $standard_feed_item;
//
//        $standard_feed_item = array();
//        $standard_feed_item['title'] = Translation :: get('TopFavorites');
//        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'top_favorites'), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $standard_feed_item['class'] = 'feed';
//        $standard_feed_items[] = $standard_feed_item;
//
//        $standard_feed_item = array();
//        $standard_feed_item['title'] = Translation :: get('MostResponded');
//        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'most_responded'), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $standard_feed_item['class'] = 'feed';
//        $standard_feed_items[] = $standard_feed_item;
//
//        $standard_feed_item = array();
//        $standard_feed_item['title'] = Translation :: get('MostRecent');
//        $standard_feed_item['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'most_recent'), array(ExternalRepositorySearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//        $standard_feed_item['class'] = 'feed';
//        $standard_feed_items[] = $standard_feed_item;
//
//        $standard_feeds['sub'] = $standard_feed_items;
//
//        $menu_items[] = $standard_feeds;

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
        return array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY);
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
            default :
                $component = $this->create_component('Browser', $this);
                $this->set_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY);
                break;
        }

        $component->run();
    }
}
?>
<?php
require_once dirname(__FILE__) . '/flickr_external_repository_connector.class.php';

class FlickrExternalRepositoryManager extends ExternalRepositoryManager
{
    
    const PARAM_FEED_TYPE = 'feed';
    
    const FEED_TYPE_GENERAL = 1;
    const FEED_TYPE_MOST_INTERESTING = 2;
    const FEED_TYPE_MOST_RECENT = 3;
    const FEED_TYPE_MY_PHOTOS = 4;

    function FlickrExternalRepositoryManager($application)
    {
        parent :: __construct($application);
        $this->set_parameter(self :: PARAM_FEED_TYPE, Request :: get(self :: PARAM_FEED_TYPE));
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'external_repository_manager/type/flickr/component/';
    }

    function initiliaze_external_repository()
    {
        FlickrExternalRepositoryConnector :: get_instance($this);
    }

    function count_external_repository_objects($condition)
    {
        return FlickrExternalRepositoryConnector :: get_instance($this)->count_external_repository_objects($condition);
    }

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        return FlickrExternalRepositoryConnector :: get_instance($this)->retrieve_external_repository_objects($condition, $order_property, $offset, $count);
    }

    function retrieve_external_repository_object($id)
    {
        return FlickrExternalRepositoryConnector :: get_instance($this)->retrieve_external_repository_object($id);
    }

    function delete_external_repository_object($id)
    {
        $connector = FlickrExternalRepositoryConnector :: get_instance($this);
        return $connector->delete_youtube_video($id);
    }

    function export_external_repository_object($object)
    {
        $connector = FlickrExternalRepositoryConnector :: get_instance($this);
        return $connector->export_youtube_video($object);
    }

    function support_sorting_direction()
    {
        return true;
    }

    function translate_search_query($query)
    {
        return FlickrExternalRepositoryConnector :: translate_search_query($query);
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
    }

    function get_menu_items()
    {
        $menu_items = array();
        
        $general = array();
        $general['title'] = Translation :: get('Browse');
        $general['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_GENERAL), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $general['class'] = 'home';
        $menu_items[] = $general;
        
        $most_recent = array();
        $most_recent['title'] = Translation :: get('MostRecent');
        $most_recent['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_MOST_RECENT), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $most_recent['class'] = 'recent';
        $menu_items[] = $most_recent;
        
        $most_interesting = array();
        $most_interesting['title'] = Translation :: get('MostInteresting');
        $most_interesting['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_MOST_INTERESTING), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $most_interesting['class'] = 'interesting';
        $menu_items[] = $most_interesting;

        $my_photos = array();
        $my_photos['title'] = Translation :: get('MyPhotos');
        $my_photos['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_MY_PHOTOS), array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        $my_photos['class'] = 'user';
        $menu_items[] = $my_photos;
        
        return $menu_items;
    }

    function is_ready_to_be_used()
    {
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
            case ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Browser', $this);
                break;
            case ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY :
                $component = $this->create_component('Viewer', $this);
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
        return array(ExternalRepositoryObjectRenderer :: TYPE_TABLE, ExternalRepositoryObjectRenderer :: TYPE_GALLERY, ExternalRepositoryObjectRenderer :: TYPE_SLIDESHOW);
    }
}
?>
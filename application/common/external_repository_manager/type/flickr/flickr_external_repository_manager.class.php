<?php
require_once dirname(__FILE__) . '/flickr_external_repository_connector.class.php';

class FlickrExternalRepositoryManager extends ExternalRepositoryManager
{

    function FlickrExternalRepositoryManager($application)
    {
        parent :: __construct($application);
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'external_repository_manager/type/flickr/component/';
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
        $connector = FlickrExternalRepositoryConnector :: get_instance($this);
        return $connector->get_youtube_video($id);
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
        return false;
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
}
?>
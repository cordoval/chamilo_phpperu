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

    function validate_settings()
    {
        return true;
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
    }

    function get_menu_items()
    {
//        $menu_items = array();

        return GoogleDocsExternalRepositoryConnector::get_instance($this)->retrieve_folders();

//        while($folder = $folders->next_result())
//        {
//            $folder_item = array();
//            $folder_item['title'] = $folder->get_title();
//            $folder_item['url'] = $this->get_url(array('folder' => $folder->get_id()));
//            //$folder_item['url'] = '#';
//            $folder_item['class'] = 'category';
//            $menu_items[] = $folder_item;
//        }
//
//        return $menu_items;
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

    function initialize_external_repository(ExternalRepositoryManager $external_repository_manager)
    {
        GoogleDocsExternalRepositoryConnector :: get_instance($this);
    }

    function get_content_object_type_conditions()
    {
        $document_conditions = array();
        $document_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.doc', Document :: get_type_name());
        $document_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.xls', Document :: get_type_name());
        $document_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.ppt', Document :: get_type_name());
        return new OrCondition($document_conditions);
    }
}
?>
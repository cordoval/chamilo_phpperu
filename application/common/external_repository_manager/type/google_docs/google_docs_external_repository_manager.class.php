<?php
require_once dirname(__FILE__) . '/google_docs_external_repository_connector.class.php';

class GoogleDocsExternalRepositoryManager extends ExternalRepositoryManager
{

    /**
     * @param Application $application
     */
    function GoogleDocsExternalRepositoryManager($external_repository, $application)
    {
        parent :: __construct($external_repository, $application);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_application_component_path()
     */
    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'external_repository_manager/type/google_docs/component/';
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#validate_settings()
     */
    function validate_settings()
    {
        return true;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#support_sorting_direction()
     */
    function support_sorting_direction()
    {
        return false;
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

    /**
     * @return array
     */
    function get_menu_items()
    {
        return $this->get_external_repository_connector()->retrieve_folders($this->get_url(array('folder' => '__PLACEHOLDER__')));
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
                $component = $this->create_component('Browser');
                $this->set_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION, ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY);
                break;
        }
        
        $component->run();
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_content_object_type_conditions()
     */
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
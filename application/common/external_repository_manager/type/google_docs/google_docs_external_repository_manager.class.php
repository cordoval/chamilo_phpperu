<?php
require_once dirname(__FILE__) . '/google_docs_external_repository_connector.class.php';

class GoogleDocsExternalRepositoryManager extends ExternalRepositoryManager
{
    const REPOSITORY_TYPE = 'google_docs';
    
    const PARAM_EXPORT_FORMAT = 'export_format';
    const PARAM_FOLDER = 'folder';

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
        $menu_items = array();
        
        $line = array();
        $line['title'] = '';
        $line['class'] = 'divider';
        
        // Basic list of all documents
        $all_items = array();
        $all_items['title'] = Translation :: get('AllItems');
        $all_items['url'] = $this->get_url(array(self :: PARAM_FOLDER => null));
        $all_items['class'] = 'home';
        
        // Special lists of documents
        $owned = array();
        $owned['title'] = Translation :: get('OwnedByMe');
        $owned['url'] = $this->get_url(array(self :: PARAM_FOLDER => GoogleDocsExternalRepositoryConnector :: DOCUMENTS_OWNED));
        $owned['class'] = 'user';
        
        $viewed = array();
        $viewed['title'] = Translation :: get('OpenedByMe');
        $viewed['url'] = $this->get_url(array(self :: PARAM_FOLDER => GoogleDocsExternalRepositoryConnector :: DOCUMENTS_VIEWED));
        $viewed['class'] = 'userview';
        
        $shared = array();
        $shared['title'] = Translation :: get('SharedWithMe');
        $shared['url'] = $this->get_url(array(self :: PARAM_FOLDER => GoogleDocsExternalRepositoryConnector :: DOCUMENTS_SHARED));
        $shared['class'] = 'external_repository';
        
        $starred = array();
        $starred['title'] = Translation :: get('Starred');
        $starred['url'] = $this->get_url(array(self :: PARAM_FOLDER => GoogleDocsExternalRepositoryConnector :: DOCUMENTS_STARRED));
        $starred['class'] = 'template';
        
        $hidden = array();
        $hidden['title'] = Translation :: get('Hidden');
        $hidden['url'] = $this->get_url(array(self :: PARAM_FOLDER => GoogleDocsExternalRepositoryConnector :: DOCUMENTS_HIDDEN));
        $hidden['class'] = 'hidden';
        
        $trashed = array();
        $trashed['title'] = Translation :: get('Trash');
        $trashed['url'] = $this->get_url(array(self :: PARAM_FOLDER => GoogleDocsExternalRepositoryConnector :: DOCUMENTS_TRASH));
        $trashed['class'] = 'trash';
        
        // Document types
        $types = array();
        $types['title'] = Translation :: get('DocumentTypes');
        $types['url'] = '#';
        $types['class'] = 'category';
        $types['sub'] = array();
        
        $pdfs = array();
        $pdfs['title'] = Translation :: get('PDFs');
        $pdfs['url'] = $this->get_url(array(self :: PARAM_FOLDER => GoogleDocsExternalRepositoryConnector :: DOCUMENTS_FILES));
        $pdfs['class'] = 'google_docs_pdf';
        $types['sub'][] = $pdfs;
        
        $documents = array();
        $documents['title'] = Translation :: get('Documents');
        $documents['url'] = $this->get_url(array(self :: PARAM_FOLDER => GoogleDocsExternalRepositoryConnector :: DOCUMENTS_DOCUMENTS));
        $documents['class'] = 'google_docs_document';
        $types['sub'][] = $documents;
        
        $presentations = array();
        $presentations['title'] = Translation :: get('Presentations');
        $presentations['url'] = $this->get_url(array(self :: PARAM_FOLDER => GoogleDocsExternalRepositoryConnector :: DOCUMENTS_PRESENTATIONS));
        $presentations['class'] = 'google_docs_presentation';
        $types['sub'][] = $presentations;
        
        $spreadsheets = array();
        $spreadsheets['title'] = Translation :: get('Spreadsheets');
        $spreadsheets['url'] = $this->get_url(array(self :: PARAM_FOLDER => GoogleDocsExternalRepositoryConnector :: DOCUMENTS_SPREADSHEETS));
        $spreadsheets['class'] = 'google_docs_spreadsheet';
        $types['sub'][] = $spreadsheets;
        
        $menu_items[] = $all_items;
        $menu_items[] = $line;
        
        $menu_items[] = $owned;
        $menu_items[] = $viewed;
        $menu_items[] = $shared;
        $menu_items[] = $starred;
        $menu_items[] = $hidden;
        $menu_items[] = $trashed;
        $menu_items[] = $types;
        
        // User defined folders
        $menu_items[] = $line;
        $folders = $this->get_external_repository_connector()->retrieve_folders($this->get_url(array(self :: PARAM_FOLDER => '__PLACEHOLDER__')));
        $menu_items = array_merge($menu_items, $folders);
        
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

    /**
     * @param ExternalRepositoryObject $object
     * @return array
     */
    function get_external_repository_object_actions(GoogleDocsExternalRepositoryObject $object)
    {
        $actions = parent :: get_external_repository_object_actions($object);
        if (in_array(ExternalRepositoryManager :: ACTION_IMPORT_EXTERNAL_REPOSITORY, array_keys($actions)))
        {
            unset($actions[ExternalRepositoryManager :: ACTION_IMPORT_EXTERNAL_REPOSITORY]);
            $export_types = $object->get_export_types();
            
            foreach ($export_types as $export_type)
            {
                $actions[$export_type] = new ToolbarItem(Translation :: get('Import' . Utilities :: underscores_to_camelcase($export_type)), Theme :: get_common_image_path() . 'external_repository/google_docs/import/' . $export_type . '.png', $this->get_url(array(self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => self :: ACTION_IMPORT_EXTERNAL_REPOSITORY, self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id(), self :: PARAM_EXPORT_FORMAT => $export_type)), ToolbarItem :: DISPLAY_ICON);
            }
        }
        
        return $actions;
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
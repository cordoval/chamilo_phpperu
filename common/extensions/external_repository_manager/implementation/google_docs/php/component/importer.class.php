<?php
class GoogleDocsExternalRepositoryManagerImporterComponent extends GoogleDocsExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function import_external_repository_object($external_object)
    {
        if ($external_object->is_importable())
        {
            $export_format = Request :: get(GoogleDocsExternalRepositoryManager :: PARAM_EXPORT_FORMAT);
            
            if (! in_array($export_format, $external_object->get_export_types()))
            {
                $export_format = 'pdf';
            }
            
            $document = ContentObject :: factory(Document :: get_type_name());
            $document->set_title($external_object->get_title());
            
            if (PlatformSetting :: get('description_required', 'repository') && StringUtilities :: is_null_or_empty($external_object->get_description()))
            {
                $document->set_description('-');
            }
            else
            {
                $document->set_description($external_object->get_description());
            }
            
            $document->set_owner_id($this->get_user_id());
            $document->set_filename(Filesystem :: create_safe_name($external_object->get_title()) . '.' . $export_format);
            
            $document->set_in_memory_file($external_object->get_content_data($export_format));
            //$document->set_in_memory_file($this->get_external_repository_connector()->download_external_repository_object($external_object, $export_format));
            
            if ($document->create())
            {
                ExternalRepositorySync :: quicksave($document, $external_object, $this->get_external_repository()->get_id());
                
                $parameters = $this->get_parameters();
                $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
                $this->redirect(Translation :: get('ImportSuccesfull'), false, $parameters, array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
                $this->redirect(Translation :: get('ImportFailed'), true, $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(null, false, $parameters);
        }
    
    }
}
?>
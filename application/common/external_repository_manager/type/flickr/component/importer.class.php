<?php
class FlickrExternalRepositoryManagerImporterComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        $importer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: IMPORTER_COMPONENT, $this);
        $importer->run();
    }

    function import_external_repository_object($external_object)
    {
        $image = ContentObject :: factory(Document :: get_type_name());
        $image->set_title($external_object->get_title());
        $image->set_description($external_object->get_description());
        $image->set_owner_id($this->get_user_id());
        $image->set_filename($external_object->get_id() . '.jpg');
        
        $sizes = $external_object->get_available_sizes();
        $image->set_in_memory_file(file_get_contents($external_object->get_url(array_pop($sizes))));
        
        if ($image->create())
        {
            $parameters = $this->get_parameters();
            $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
            $this->redirect(Translation :: get('ImportSuccesfull'), false, $parameters, array(ExternalRepositoryManager :: PARAM_TYPE, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(Translation :: get('ImportFailed'), true, $parameters);
        }
    
    }
}
?>
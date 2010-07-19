<?php
class YoutubeExternalRepositoryManagerImporterComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        $importer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: IMPORTER_COMPONENT, $this);
        $importer->run();
    }

    function import_external_repository_object($object)
    {
        $youtube = ContentObject :: factory(Youtube :: get_type_name());
        $youtube->set_title($object->get_title());
        $youtube->set_description($object->get_description());
        $youtube->set_url('http://www.youtube.com/watch?v=' . $object->get_id());
        $youtube->set_height(344);
        $youtube->set_width(425);
        $youtube->set_owner_id($this->get_user_id());
        if ($youtube->create())
        {
            ExternalRepositorySync :: quicksave($youtube, $object, $this->get_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY));
            $parameters = $this->get_parameters();
            $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
            $this->redirect(Translation :: get('ImportSuccesfull'), false, $parameters, array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION));
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            $this->redirect(Translation :: get('ImportFailed'), true, $parameters);
        }
    
    }
}
?>
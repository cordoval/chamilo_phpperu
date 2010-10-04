<?php
class MatterhornExternalRepositoryManagerImporterComponent extends MatterhornExternalRepositoryManager
{
	function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

    function import_external_repository_object($object)
    {
        if ($object->is_importable())
        {
           
            $streaming_video_clip = ContentObject :: factory(Matterhorn :: get_type_name());
            $streaming_video_clip->set_title($object->get_title());
            $streaming_video_clip->set_description($object->get_description());
            $streaming_video_clip->set_owner_id($this->get_user_id());
            $streaming_video_clip->set_matterhorn_id($object->get_id());
            $streaming_video_clip->set_thumbnail($object->get_search_preview()->get_url());
            
            if ($streaming_video_clip->create())
            {
                ExternalRepositorySync :: quicksave($streaming_video_clip, $object, $this->get_external_repository()->get_id());
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
        else
        {
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            $this->redirect(null, false, $parameters);
        }
    }
}
?>
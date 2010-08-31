<?php

/**
 * Description of MediamosaForm class
 *
 * @author jevdheyd
 */

require_once Path :: get_application_path() . 'common/external_repository_manager/type/mediamosa/mediamosa_external_repository_object.class.php';

class MediamosaForm extends ContentObjectForm
{

    function build_creation_form()
    {
        parent :: build_creation_form();

        $this->addElement('hidden', ExternalRepositoryObject :: PROPERTY_EXTERNAL_REPOSITORY_ID);
        $this->addElement('hidden', ExternalRepositoryObject :: PROPERTY_ID);

        $rdm = RepositoryDataManager :: get_instance();
        $external_repositories = $rdm->retrieve_external_repositories();

        while($external_repository = $external_repositories->next_result())
        {
            if($external_repository->get_type() == 'mediamosa')
            {
                $link = PATH :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=' . ExternalRepositoryLauncher :: APPLICATION_NAME . '&' . ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY . '=' . $external_repository->get_id();
                $this->addElement('static', null, null, '<a class="button normal_button upload_button" onclick="javascript:openPopup(\'' . $link . '\');"> ' . Translation :: get('Browse') .' '. $external_repository->get_title() . '</a>');
            }
        }

        //$this->streaming_video_clip_form_elements();
    }

    function build_editing_form()
    {
        parent :: build_editing_form();
        
        //$this->streaming_video_clip_form_elements();
    }

    function create_content_object()
    {
        if(parent :: create_content_object())
        {
            $mediamosa = $this->get_content_object();

            $external_respository_sync = new ExternalRepositorySync();
            $external_respository_sync->set_external_repository_id($this->exportValue(ExternalRepositoryObject :: PROPERTY_EXTERNAL_REPOSITORY_ID));
            $external_respository_sync->set_external_repository_object_id($this->exportValue(ExternalRepositoryObject :: PROPERTY_ID));
            
            $object = $external_respository_sync->get_external_repository_object();
            
            ExternalRepositorySync :: quicksave($mediamosa, $object, $this->exportValue(ExternalRepositoryObject :: PROPERTY_EXTERNAL_REPOSITORY_ID));

            return $mediamosa;
         }
    }
}
?>
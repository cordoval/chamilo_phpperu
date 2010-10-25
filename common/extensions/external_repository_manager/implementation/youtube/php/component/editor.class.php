<?php
namespace common\extensions\external_repository_manager\implementation\youtube;
use common\libraries\Path;
use common\libraries\Request;

require_once dirname(__FILE__) . '/../forms/youtube_external_repository_manager_form.class.php';

class YoutubeExternalRepositoryManagerEditorComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        $id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $form = new YoutubeExternalRepositoryManagerForm(YoutubeExternalRepositoryManagerForm :: TYPE_EDIT, $this->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)), $this);
        
        $object = $this->retrieve_external_repository_object($id);
        
        $form->set_external_repository_object($object);
        
        if ($form->validate())
        {
            $success = $form->update_video_entry();
            
            $parameters = $this->get_parameters();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            
            if ($this->is_stand_alone())
            {
                Redirect :: web_link(Path :: get(WEB_PATH) . 'common/launcher/index.php', $parameters);
            }
            else
            {
                Redirect :: web_link(Path :: get(WEB_PATH) . 'core.php', $parameters);
            }
        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }
}
?>
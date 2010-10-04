<?php
require_once dirname(__FILE__) . '/../forms/hq23_external_repository_manager_form.class.php';

class Hq23ExternalRepositoryManagerUploaderComponent extends Hq23ExternalRepositoryManager
{

    function run()
    {
        $form = new Hq23ExternalRepositoryManagerForm(Hq23ExternalRepositoryManagerForm :: TYPE_CREATE, $this->get_url(), $this);
        
        if ($form->validate())
        {
            $id = $form->upload_photo();
            
            if ($id)
            {
                $parameters = $this->get_parameters();
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID] = $id;
                
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
                Request :: set_get(Application :: PARAM_ERROR_MESSAGE, Translation :: get('Hq23UploadProblem'));
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
}
?>
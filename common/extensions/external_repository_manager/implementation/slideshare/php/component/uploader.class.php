<?php
namespace common\extensions\external_repository_manager\implementation\slideshare;

use common\extensions\external_repository_manager\ExternalRepositoryManager;

use common\libraries\Request;
use common\libraries\Redirect;
use common\libraries\Path;
use common\libraries\Application;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/../forms/slideshare_external_repository_manager_form.class.php';

class SlideshareExternalRepositoryManagerUploaderComponent extends SlideshareExternalRepositoryManager
{

    function run()
    {
        $form = new SlideshareExternalRepositoryManagerForm(SlideshareExternalRepositoryManagerForm :: TYPE_CREATE, $this->get_url(), $this);
        
        if ($form->validate())
        {
            $id = $form->upload_slideshow();
            
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
                Request :: set_get(Application :: PARAM_ERROR_MESSAGE, Translation :: get('SlideshareUploadProblem'));
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
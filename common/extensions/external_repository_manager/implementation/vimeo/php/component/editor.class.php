<?php
namespace common\extensions\external_repository_manager\implementation\vimeo;

use common\libraries\Path;
use common\libraries\Redirect;
use common\libraries\Request;
use common\extensions\external_repository_manager\ExternalRepositoryManager;

require_once dirname(__FILE__) . '/../forms/flickr_external_repository_manager_form.class.php';

class VimeoExternalRepositoryManagerEditorComponent extends VimeoExternalRepositoryManager
{

    function run()
    {
        $id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $form = new VimeoExternalRepositoryManagerForm(VimeoExternalRepositoryManagerForm :: TYPE_EDIT, $this->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)), $this);

        $object = $this->retrieve_external_repository_object($id);

        $form->set_external_repository_object($object);

        if ($form->validate())
        {
            $success = $form->update_photo();

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
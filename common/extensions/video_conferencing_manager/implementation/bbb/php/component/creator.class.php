<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\libraries\Request;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Redirect;
use common\libraries\Path;

use common\extensions\video_conferencing_manager\VideoConferencingComponent;

require_once dirname(__FILE__) . '/../forms/bbb_video_conferencing_manager_form.class.php';

class BbbVideoConferencingManagerCreatorComponent extends BbbVideoConferencingManager
{

    function run()
    {
        $form = new BbbVideoConferencingManagerForm(BbbVideoConferencingManagerForm :: TYPE_CREATE, $this->get_url(), $this);
        $object = new BbbVideoConferencingObject();
        $form->set_video_conferencing_object($object);
        
        if ($form->validate())
        {
            $result = $form->create_meeting();
            
            if ($result instanceof BbbVideoConferencingObject)
            {
                $parameters = $this->get_parameters();
                $parameters[BbbVideoConferencingManager :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION] = BbbVideoConferencingManager :: ACTION_CREATE_MEETING;
                $parameters[BbbVideoConferencingManager :: PARAM_VIDEO_CONFERENCING_ID] = $result->get_id();
                
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
                Request :: set_get(Application :: PARAM_ERROR_MESSAGE, $result);
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
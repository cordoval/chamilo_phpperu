<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/../forms/video_conferencing_form.class.php';

class VideoConferencingInstanceManagerUpdaterComponent extends VideoConferencingInstanceManager
{

    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
        }

        $instance_id = Request :: get(VideoConferencingInstanceManager :: PARAM_INSTANCE);

        if(isset($instance_id))
        {
            $video_conferencing = $this->retrieve_video_conferencing($instance_id);
            $form = new VideoConferencingForm(VideoConferencingForm :: TYPE_EDIT, $video_conferencing, $this->get_url(array(VideoConferencingInstanceManager :: PARAM_INSTANCE => $instance_id)));

            if ($form->validate())
            {
                $success = $form->update_video_conferencing();
                $this->redirect(Translation :: get($success ? 'ObjectUpdated' : 'ObjectNotUpdated', array('OBJECT' => Translation :: get('VideoConferencing')), Utilities :: COMMON_LIBRARIES), ($success ? false : true), array(VideoConferencingInstanceManager :: PARAM_INSTANCE_ACTION => VideoConferencingInstanceManager :: ACTION_BROWSE_INSTANCES));
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
                $this->display_header();
                $this->display_error_message(Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('VideoConferencing')), Utilities :: COMMON_LIBRARIES));
                $this->display_footer();
        }
    }
}
?>
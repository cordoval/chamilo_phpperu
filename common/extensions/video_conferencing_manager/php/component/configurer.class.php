<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/../forms/video_conferencing_settings_form.class.php';

class VideoConferencingComponentConfigurerComponent extends VideoConferencingComponent
{

    function run()
    {
        $video_conferencing_id = $this->get_parameter(VideoConferencingManager :: PARAM_VIDEO_CONFERENCING);

        $form = new VideoConferencingSettingsForm($this, $video_conferencing_id, 'config', 'post', $this->get_url());
        if ($form->validate())
        {
            $success = $form->update_configuration();
            $this->redirect(Translation :: get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), ($success ? false : true));
        }
        else
        {
            $this->display_header();
            $form->display();
            echo '<script type="text/javascript">';
            echo '$(document).ready(function() {';
            echo '$(\':checkbox\').iphoneStyle({ checkedLabel: \'' . Translation :: get('ConfirmOn', null, Utilities :: COMMON_LIBRARIES) . '\', uncheckedLabel: \'' . Translation :: get('ConfirmOff', null, Utilities :: COMMON_LIBRARIES) . '\'});';
            echo '});';
            echo '</script>';
            $this->display_footer();
        }
    }
}
?>
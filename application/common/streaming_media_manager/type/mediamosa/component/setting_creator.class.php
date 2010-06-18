<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaManagerSettingCreatorComponent extends MediamosaStreamingMediaManager {

    function run()
    {
        $form = MediamosaStreamingMediaManagerSettingsForm(MediamosaStreamingMediaManagerSettingsForm :: TYPE_CREATE, $this->get_url());

        if($form->validate)
        {
            $parameters = array();
            $parameters[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = MediamosaStreamingMediaManager :: ACTION_MANAGE_SETTINGS;

            if($form->create_setting())
            {
                $this->redirect(Translation :: get('Setting created'), false, $parameters);
            }
            else
            {
                $this->redirect(Translation :: get('Setting not created'), true, $parameters);
            }
        }else{
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }
}
?>

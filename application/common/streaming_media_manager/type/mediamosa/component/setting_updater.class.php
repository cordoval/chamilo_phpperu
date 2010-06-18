<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaManagerSettingDeleterComponent extends MediamosaStreamingMediaManager {

    function run()
    {
        $form = MediamosaStreamingMediaManagerSettingsForm(MediamosaStreamingMediaManagerSettingsForm :: TYPE_EDIT, $this->get_url());

        //TODO:jens iplement setting retrieval
        $setting = $this->retrieve_setting(Request :: get(MediamosaStreamingMediaManager :: PARAM_STREAMING_MEDIA_SETTING_ID));
        $form->set_server_object($setting);

        if($form->validate)
        {
            $parameters = array();
            $parameters[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = MediamosaStreamingMediaManager :: ACTION_MANAGE_SETTINGS;

            if($form->create_setting())
            {
                $this->redirect(Translation :: get('Setting updated'), false, $parameters);
            }
            else
            {
                $this->redirect(Translation :: get('Setting not update'), true, $parameters);
            }
        }else{
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }
}
?>

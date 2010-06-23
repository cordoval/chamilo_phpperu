<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/../mediamosa_streaming_media_server_object.class.php';
require_once dirname(__FILE__) . '/../forms/mediamosa_streaming_media_manager_settings_form.class.php';
require_once dirname(__FILE__) . '/../mediamosa_streaming_media_data_manager.class.php';

class MediamosaStreamingMediaManagerSettingCreatorComponent extends MediamosaStreamingMediaManager {

    function run()
    {
        $form = new MediamosaStreamingMediaManagerSettingsForm(MediamosaStreamingMediaManagerSettingsForm :: TYPE_CREATE, $this->get_url(), $this);

        if($form->validate())
        {
            $parameters = array();
            $parameters[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = MediamosaStreamingMediaManager :: ACTION_MANAGE_SETTINGS;

            if($form->create_setting())
            {
                $connector = new MediamosaStreamingMediaConnector($form->exportValue(StreamingMediaServerObject :: PROPERTY_ID), false);
                
                if($connector->login())
                {
                    $this->redirect(Translation :: get('Setting created Login Succeeded'), false, $parameters);
                }
                else
                {
                    $this->redirect(Translation :: get('Setting created Login Failed'), true, $parameters);
                }
            }
            else
            {
                $this->redirect(Translation :: get('Setting not created'), true, $parameters);
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

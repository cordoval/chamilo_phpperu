<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/../mediamosa_streaming_media_server_object.class.php';
require_once dirname(__FILE__) . '/../forms/mediamosa_streaming_media_manager_settings_form.class.php';
require_once dirname(__FILE__) . '/../mediamosa_streaming_media_data_manager.class.php';

class MediamosaStreamingMediaManagerSettingUpdaterComponent extends MediamosaStreamingMediaManager {

    function run()
    {
        $form = new MediamosaStreamingMediaManagerSettingsForm(MediamosaStreamingMediaManagerSettingsForm :: TYPE_EDIT, $this->get_url());

        //TODO:jens iplement setting retrieval
        $dm = MediamosaStreamingMediaDataManager :: get_instance();
        $setting = $dm->retrieve_streaming_media_server_object(Request :: get(MediamosaStreamingMediaManager :: PARAM_SERVER));

        $form->set_server_object($setting);

        if($form->validate())
        {
            $parameters = array();
            $parameters[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = MediamosaStreamingMediaManager :: ACTION_MANAGE_SETTINGS;

            if($form->update_setting())
            {
                $this->redirect(Translation :: get('Setting updated'), true, $parameters);
            }
            else
            {
                $this->redirect(Translation :: get('Setting not update'), false, $parameters);
            }
        }else{
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }



}
?>

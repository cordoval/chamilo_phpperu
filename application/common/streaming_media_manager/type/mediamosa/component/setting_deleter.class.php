<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaManagerSettingCreatorComponent extends MediamosaStreamingMediaManager {

    function run()
    {
        $parameters = array();
        $parameters[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = MediamosaStreamingMediaManager :: ACTION_MANAGE_SETTINGS;

        if($this->delete_setting(Request :: get(MediamosaStreamingMediaManager :: PARAM_STREAMING_MEDIA_SETTING_ID)))
        {
            $this->redirect(Translation :: get('Setting deleted'), false, $parameters);
        }
        else
        {
            $this->redirect(Translation :: get('Setting not deleted'), true, $parameters);
        }
    }
}
?>

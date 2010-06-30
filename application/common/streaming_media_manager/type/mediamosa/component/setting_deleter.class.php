<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/../mediamosa_streaming_media_server_object.class.php';
require_once dirname(__FILE__) . '/../mediamosa_streaming_media_data_manager.class.php';

class MediamosaStreamingMediaManagerSettingDeleterComponent extends MediamosaStreamingMediaManager {

    function run()
    {
        $parameters = array();
        $parameters[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = MediamosaStreamingMediaManager :: ACTION_MANAGE_SETTINGS;

        $object = new StreamingMediaServerObject();
        $object->set_id(Request :: get(MediamosaStreamingMediaManager :: PARAM_SERVER));

        if($object->delete())
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

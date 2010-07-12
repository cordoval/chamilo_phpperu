<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/../mediamosa_external_repository_server_object.class.php';
require_once dirname(__FILE__) . '/../mediamosa_external_repository_data_manager.class.php';

class MediamosaExternalRepositoryManagerSettingDeleterComponent extends MediamosaExternalRepositoryManager {

    function run()
    {
        $parameters = array();
        $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = MediamosaExternalRepositoryManager :: ACTION_MANAGE_SETTINGS;

        $object = new ExternalRepositoryServerObject();
        $object->set_id(Request :: get(MediamosaExternalRepositoryManager :: PARAM_SERVER));

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

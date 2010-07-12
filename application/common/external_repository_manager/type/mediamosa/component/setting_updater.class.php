<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/../mediamosa_external_repository_server_object.class.php';
require_once dirname(__FILE__) . '/../forms/mediamosa_external_repository_manager_settings_form.class.php';
require_once dirname(__FILE__) . '/../mediamosa_external_repository_data_manager.class.php';

class MediamosaExternalRepositoryManagerSettingUpdaterComponent extends MediamosaExternalRepositoryManager {

    function run()
    {
        $form = new MediamosaExternalRepositoryManagerSettingsForm(MediamosaExternalRepositoryManagerSettingsForm :: TYPE_EDIT, $this->get_url());

        //TODO:jens iplement setting retrieval
        $dm = MediamosaExternalRepositoryDataManager :: get_instance();
        $setting = $dm->retrieve_external_repository_server_object(Request :: get(MediamosaExternalRepositoryManager :: PARAM_SERVER));

        $form->set_server_object($setting);

        if($form->validate())
        {
            $parameters = array();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = MediamosaExternalRepositoryManager :: ACTION_MANAGE_SETTINGS;

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

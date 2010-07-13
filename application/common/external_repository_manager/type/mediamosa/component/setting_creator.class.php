<?php
/**
 * Description of admin_default_settings_creatorclass
 *
 * @author jevdheyd
 */
require_once dirname(__FILE__) . '/../mediamosa_external_repository_server_object.class.php';
require_once dirname(__FILE__) . '/../forms/mediamosa_external_repository_manager_settings_form.class.php';
require_once dirname(__FILE__) . '/../mediamosa_external_repository_data_manager.class.php';

class MediamosaExternalRepositoryManagerSettingCreatorComponent extends MediamosaExternalRepositoryManager {

    function run()
    {
        $form = new MediamosaExternalRepositoryManagerSettingsForm(MediamosaExternalRepositoryManagerSettingsForm :: TYPE_CREATE, $this->get_url(), $this);

        if($form->validate())
        {
            $parameters = array();
            $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = MediamosaExternalRepositoryManager :: ACTION_MANAGE_SETTINGS;

            if($form->create_setting())
            {
                $connector = new MediamosaExternalRepositoryConnector($form->exportValue(ExternalRepositoryServerObject :: PROPERTY_ID), false);
                
                if($connector->login())
                {
                    $this->redirect(Translation :: get('Created') . Translation :: get('Success'), false, $parameters);
                }
                else
                {
                    $this->redirect(Translation :: get('Created') . Translation :: get('Failed'), false, $parameters);
                }
            }
            else
            {
                $this->redirect(Translation :: get('NotCreated'), true, $parameters);
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

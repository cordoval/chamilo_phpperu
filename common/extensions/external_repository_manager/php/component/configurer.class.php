<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Translation;
use common\libraries\Utilities;

use repository\RepositoryManager;

require_once dirname(__FILE__) . '/../forms/external_repository_settings_form.class.php';

class ExternalRepositoryComponentConfigurerComponent extends ExternalRepositoryComponent
{

    function run()
    {
        $external_repository_id = $this->get_parameter(RepositoryManager :: PARAM_EXTERNAL_INSTANCE);

        $form = new ExternalRepositorySettingsForm($this, $external_repository_id, 'config', 'post', $this->get_url());
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
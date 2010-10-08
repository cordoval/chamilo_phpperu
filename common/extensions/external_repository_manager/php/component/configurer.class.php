<?php
require_once dirname(__FILE__) . '/../forms/external_repository_settings_form.class.php';

class ExternalRepositoryComponentConfigurerComponent extends ExternalRepositoryComponent
{

    function run()
    {
        $external_repository_id = $this->get_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);

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
            echo '$(\':checkbox\').iphoneStyle({ checkedLabel: \'' . Translation :: get('On') . '\', uncheckedLabel: \'' . Translation :: get('Off') . '\'});';
            echo '});';
            echo '</script>';
            $this->display_footer();
        }
    }
}
?>
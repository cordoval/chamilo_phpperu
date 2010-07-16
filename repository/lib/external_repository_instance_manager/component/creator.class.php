<?php
require_once dirname(__FILE__) . '/../forms/external_repository_form.class.php';

class ExternalRepositoryInstanceManagerCreatorComponent extends ExternalRepositoryInstanceManager
{

    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
        }
        
        $external_repository = new ExternalRepository();
        $form = new ExternalRepositoryForm(ExternalRepositoryForm :: TYPE_CREATE, $external_repository, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->create_external_repository();
            $this->redirect(Translation :: get($success ? 'ExternalRepositoryAdded' : 'ExternalRepositoryNotAdded'), ($success ? false : true), array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE_ACTION => ExternalRepositoryInstanceManager :: ACTION_BROWSE_INSTANCES));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
}
?>
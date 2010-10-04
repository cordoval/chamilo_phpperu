<?php
require_once dirname(__FILE__) . '/../forms/external_repository_form.class.php';

class ExternalRepositoryInstanceManagerUpdaterComponent extends ExternalRepositoryInstanceManager
{

    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
        }
        
        $instance_id = Request :: get(ExternalRepositoryInstanceManager :: PARAM_INSTANCE);
        
        if(isset($instance_id))
        {
            $external_repository = $this->retrieve_external_repository($instance_id);
            $form = new ExternalRepositoryForm(ExternalRepositoryForm :: TYPE_EDIT, $external_repository, $this->get_url(array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE => $instance_id)));
            
            if ($form->validate())
            {
                $success = $form->update_external_repository();
                $this->redirect(Translation :: get($success ? 'ExternalRepositoryUpdated' : 'ExternalRepositoryNotUpdated'), ($success ? false : true), array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE_ACTION => ExternalRepositoryInstanceManager :: ACTION_BROWSE_INSTANCES));
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
                $this->display_header();
                $this->display_error_message(Translation :: get('NoExternalRepositorySelected'));
                $this->display_footer();
        }
    }
}
?>
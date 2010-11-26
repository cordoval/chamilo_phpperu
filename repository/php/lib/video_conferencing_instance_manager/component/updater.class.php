<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/../forms/external_instance_form.class.php';

class ExternalInstanceInstanceManagerUpdaterComponent extends ExternalInstanceInstanceManager
{

    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
        }

        $instance_id = Request :: get(ExternalInstanceInstanceManager :: PARAM_INSTANCE);

        if(isset($instance_id))
        {
            $external_instance = $this->retrieve_external_instance($instance_id);
            $form = new ExternalInstanceForm(ExternalInstanceForm :: TYPE_EDIT, $external_instance, $this->get_url(array(ExternalInstanceInstanceManager :: PARAM_INSTANCE => $instance_id)));

            if ($form->validate())
            {
                $success = $form->update_external_instance();
                $this->redirect(Translation :: get($success ? 'ObjectUpdated' : 'ObjectNotUpdated', array('OBJECT' => Translation :: get('ExternalInstance')), Utilities :: COMMON_LIBRARIES), ($success ? false : true), array(ExternalInstanceInstanceManager :: PARAM_INSTANCE_ACTION => ExternalInstanceInstanceManager :: ACTION_BROWSE_INSTANCES));
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
                $this->display_error_message(Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('ExternalInstance')), Utilities :: COMMON_LIBRARIES));
                $this->display_footer();
        }
    }
}
?>
<?php
require_once dirname(__FILE__) . '/../../forms/cas_user_request_form.class.php';

class CasUserManagerCreatorComponent extends CasUserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $cas_user_request = new CasUserRequest();
        $form = new CasUserRequestForm(CasUserRequestForm :: TYPE_CREATE, $cas_user_request, $this->get_url(), $this->get_user());

        if ($form->validate())
        {
            $success = $form->create_cas_user_request();
            if ($success)
            {
                $this->redirect(Translation :: get('CasUserRequestCreated'), (false), array(Application :: PARAM_ACTION => CasUserManager :: ACTION_VIEW, CasUserManager :: PARAM_REQUEST_ID => $cas_user_request->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('CasUserRequestNotCreated'), (true), array(Application :: PARAM_ACTION => CasUserManager :: ACTION_BROWSE));
            }
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
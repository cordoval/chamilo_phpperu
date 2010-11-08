<?php
namespace application\cas_user;

use common\libraries\Request;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/../../forms/cas_user_request_form.class.php';

/**
 * @author Hans De Bisschop
 */
class CasUserManagerEditorComponent extends CasUserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $cas_user_request = CasUserDataManager :: get_instance()->retrieve_cas_user_request(Request :: get(CasUserManager :: PARAM_REQUEST_ID));
        $form = new CasUserRequestForm(CasUserRequestForm :: TYPE_EDIT, $cas_user_request, $this->get_url(array(CasUserManager :: PARAM_REQUEST_ID => $cas_user_request->get_id())), $this->get_user());

        if ($form->validate())
        {
            $success = $form->update_cas_user_request();
            $this->redirect($success ? Translation :: get('CasUserRequestUpdated', null, Utilities::COMMON_LIBRARIES) : Translation :: get('CasUserRequestNotUpdated', null, Utilities::COMMON_LIBRARIES), ! $success, array(CasUserManager :: PARAM_ACTION => CasUserManager :: ACTION_BROWSE));
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
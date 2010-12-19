<?php
namespace application\cas_user;

use common\libraries\Utilities;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/../../forms/cas_account_form.class.php';

class CasAccountManagerCreatorComponent extends CasAccountManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $cas_account = new CasAccount();
        $form = new CasAccountForm(CasAccountForm :: TYPE_CREATE, $cas_account, $this->get_url(), $this->get_user());

        if ($form->validate())
        {
            $success = $form->create_cas_account();
            if ($success)
            {
                $this->redirect(Translation :: get('CasAccountCreated', null, Utilities :: COMMON_LIBRARIES), (false), array(
                        CasAccountManager :: PARAM_CAS_ACCOUNT_ACTION => CasAccountManager :: ACTION_BROWSE,
                        CasAccountManager :: PARAM_ACCOUNT_ID => $cas_account->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('CasAccountNotCreated', null, Utilities :: COMMON_LIBRARIES), (true), array(
                        CasAccountManager :: PARAM_CAS_ACCOUNT_ACTION => CasAccountManager :: ACTION_BROWSE));
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
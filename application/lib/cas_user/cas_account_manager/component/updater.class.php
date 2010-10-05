<?php
require_once dirname(__FILE__) . '/../../forms/cas_account_form.class.php';

/**
 * @author Hans De Bisschop
 */
class CasAccountManagerUpdaterComponent extends CasUserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $cas_account = CasAccountDataManager :: get_instance()->retrieve_cas_account(Request :: get(CasAccountManager :: PARAM_ACCOUNT_ID));
        $form = new CasAccountForm(CasAccountForm :: TYPE_EDIT, $cas_account, $this->get_url(array(CasAccountManager :: PARAM_ACCOUNT_ID => $cas_account->get_id())), $this->get_user());

        if ($form->validate())
        {
            $success = $form->update_cas_account();
            $this->redirect($success ? Translation :: get('CasAccountUpdated') : Translation :: get('CasAccountNotUpdated'), ! $success, array(CasAccountManager :: PARAM_CAS_ACCOUNT_ACTION => CasAccountManager :: ACTION_BROWSE));
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
<?php
/**
 * @author Hans De Bisschop
 */
class CasAccountManagerActivaterComponent extends CasAccountManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $ids = Request :: get(CasAccountManager :: PARAM_ACCOUNT_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $cas_account = CasAccountDataManager :: get_instance()->retrieve_cas_account($id);
                $cas_account->set_status(CasAccount :: STATUS_ENABLED);
                if (! $cas_account->update())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasAccountNotActivated';
                }
                else
                {
                    $message = 'SelectedCasAccountsNotActivated';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasAccountActivated';
                }
                else
                {
                    $message = 'SelectedCasAccountsActivated';
                }
            }

            $this->redirect(Translation :: get($message), ($failures ? true : false), array(CasAccountManager :: PARAM_CAS_ACCOUNT_ACTION => CasAccountManager :: ACTION_BROWSE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCasAccountSelected')));
        }
    }
}
?>
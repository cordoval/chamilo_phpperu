<?php
/**
 * @author Hans De Bisschop
 */
class CasUserManagerAccepterComponent extends CasUserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $ids = Request :: get(CasUserManager :: PARAM_REQUEST_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $cas_user_request = $cas_user_request = CasUserDataManager :: get_instance()->retrieve_cas_user_request($id);

                if (! $cas_user_request->generate_cas_account())
                {
                    $failures ++;
                }
                else
                {
                    $cas_user_request->set_status(CasUserRequest :: STATUS_ACCEPTED);
                    if (! $cas_user_request->update())
                    {
                        // We shouldn't do this ... the account WAS created ?!
                    // return false;
                    }
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasUserRequestNotAccepted';
                }
                else
                {
                    $message = 'SelectedCasUserRequestsNotAccepted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasUserRequestAccepted';
                }
                else
                {
                    $message = 'SelectedCasUserRequestsAccepted';
                }
            }

            $this->redirect(Translation :: get($message), ($failures ? true : false), array(CasUserManager :: PARAM_ACTION => CasUserManager :: ACTION_BROWSE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCasUserRequestSelected')));
        }
    }
}
?>
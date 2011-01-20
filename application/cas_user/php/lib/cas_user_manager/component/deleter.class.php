<?php
namespace application\cas_user;

use common\libraries\Utilities;
use common\libraries\Request;
use common\libraries\Translation;

/**
 * @author Hans De Bisschop
 */
class CasUserManagerDeleterComponent extends CasUserManager
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

                if (! $cas_user_request->delete())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasUserRequestNotDeleted';
                }
                else
                {
                    $message = 'SelectedCasUserRequestsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasUserRequestDeleted';
                }
                else
                {
                    $message = 'SelectedCasUserRequestsDeleted';
                }
            }

            $this->redirect(Translation :: get($message, null, Utilities :: COMMON_LIBRARIES), ($failures ? true : false), array(
                    CasUserManager :: PARAM_ACTION => CasUserManager :: ACTION_BROWSE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCasUserRequestSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }
}
?>
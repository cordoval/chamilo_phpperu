<?php
namespace user;

use tracking\ChangesTracker;
use tracking\Event;

use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Display;
use common\libraries\AdministrationComponent;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Text;
use common\libraries\Hashing;
use common\libraries\Mail;
use common\libraries\Utilities;

/**
 * $Id: deleter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerMultiPasswordResetterComponent extends UserManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(UserManager :: PARAM_USER_USER_ID);

        if (! UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, 0))
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add_help('user general');
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        if (count($ids) > 0)
        {
            $failures = 0;

            foreach ($ids as $id)
            {
                $user = $this->retrieve_user($id);

                $password = Text :: generate_password();
                $user->set_password(Hashing :: hash($password));

                if ($user->update())
                {
                    $mail_subject = Translation :: get('LoginRequest');
                    $mail_body[] = $user->get_fullname() . ',';
                    $mail_body[] = Translation :: get('YourAccountParam') . ' ' . $this->get_path(WEB_PATH);
                    $mail_body[] = Translation :: get('UserName') . ' :' . $user->get_username();
                    $mail_body[] = Translation :: get('Password') . ' :' . $password;
                    $mail_body = implode("\n", $mail_body);
                    $mail = Mail :: factory($mail_subject, $mail_body, $user->get_email());
                    $mail->send();

                    Event :: trigger('update', UserManager :: APPLICATION_NAME, array(
                            ChangesTracker :: PROPERTY_REFERENCE_ID => $user->get_id(),
                            ChangesTracker :: PROPERTY_USER_ID => $this->get_user()->get_id()));
                }
                else
                {
                    $failures ++;
                }
            }

            $message = $this->get_result($failures, count($ids), 'UserPasswordNotResetted', 'UserPasswordsNotResetted', 'UserPasswordResetted', 'UserPasswordsResetted');

            $this->redirect($message, ($failures > 0), array(
                    Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));

        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array(
                    'OBJECT' => Translation :: get('User')), Utilities :: COMMON_LIBRARIES)));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserManagerAdminUserBrowserComponent')));
        $breadcrumbtrail->add_help('user_password_resetter');
    }

    function get_additional_parameters()
    {
        return array(UserManager :: PARAM_USER_USER_ID);
    }
}
?>
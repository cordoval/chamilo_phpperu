<?php
namespace user;

use common\libraries\Application;

use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Display;
use common\libraries\AdministrationComponent;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;

/**
 * $Id: quota.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerQuotaComponent extends UserManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user_id = $this->get_user_id();

        $id = Request :: get(UserManager :: PARAM_USER_USER_ID);

        if ($id)
        {
            if (! UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, $id))
            {
                $this->display_header();
                Display :: error_message(Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES));
                $this->display_footer();
                exit();
            }

            $user = $this->retrieve_user($id);

            if (! $this->get_user()->is_platform_admin())
            {
                $this->display_header();
                Display :: error_message(Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES));
                $this->display_footer();
                exit();
            }
            $form = new UserQuotaForm($user, $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $id)));

            if ($form->validate())
            {
                $success = $form->update_quota();
                $this->redirect(Translation :: get($success ? 'UserQuotaUpdated' : 'UserQuotaNotUpdated'), ($success ? false : true), array(
                        Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_QUOTA));
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
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array(
                    'OBJECT' => Translation :: get('User')), Utilities :: COMMON_LIBRARIES)));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserManagerAdminUserBrowserComponent')));
        $breadcrumbtrail->add_help('user_quota');
    }

    function get_additional_parameters()
    {
        return array(UserManager :: PARAM_USER_USER_ID);
    }
}
?>
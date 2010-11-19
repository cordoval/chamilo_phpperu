<?php
namespace user;

use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Display;
use common\libraries\AdministrationComponent;
use common\libraries\Breadcrumb;
use common\libraries\Session;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;

/**
 * $Id: change_user.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerChangeUserComponent extends UserManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(UserManager :: PARAM_USER_USER_ID);
        if ($id)
        {
        	if (!UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, $id))
		    {
		      	$this->display_header();
		        Display :: error_message(Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES));
		        $this->display_footer();
		        exit();
		    }

		    $checkurl = Session :: retrieve('checkChamiloURL');
		    Session :: clear();
            Session :: register('_uid', $id);
            Session :: register('_as_admin', $this->get_user_id());
            Session :: register('checkChamiloURL', $checkurl);
            header('Location: index.php');

        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array('OBJECT'=> Translation :: get('User')), Utilities :: COMMON_LIBRARIES)));
        }
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserManagerAdminUserBrowserComponent')));
    	$breadcrumbtrail->add_help('user_changer');
    }

    function get_additional_parameters()
    {
    	return array(UserManager :: PARAM_USER_USER_ID);
    }
}
?>
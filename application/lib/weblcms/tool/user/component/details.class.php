<?php

/**
 * $Id: user_details.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */
require_once dirname(__FILE__) . '/../user_tool.class.php';
require_once dirname(__FILE__) . '/../../../../../common/user_details.class.php';

class UserToolDetailsComponent extends UserTool
{

    function run()
    {
        if (!$this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        $trail = BreadcrumbTrail :: get_instance();
        

        if (Request :: get('users') != null)
        {
            $user = UserDataManager :: get_instance()->retrieve_user(Request :: get('users'));
            //$trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'user_details', 'users' => Request :: get('users'))), $user->get_firstname() . ' ' . $user->get_lastname()));
        }
        //$trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'user_details', 'users' => Request :: get('users'))), Translation :: get('Details')));
        $this->display_header();

        $udm = UserDataManager :: get_instance();
        if (Request :: get(WeblcmsManager :: PARAM_USERS))
        {
            $user = $udm->retrieve_user(Request :: get(WeblcmsManager :: PARAM_USERS));
            $details = new UserDetails($user);
            echo $details->toHtml();
        }
        if (isset($_POST['user_id']))
        {
            foreach ($_POST['user_id'] as $index => $user_id)
            {
                $user = $udm->retrieve_user($user_id);
                $details = new UserDetails($user);
                echo $details->toHtml();
            }
        }

        $this->display_footer();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USER_BROWSER)), Translation :: get('UserToolUnsubscribeUserBrowserComponent')));
        $breadcrumbtrail->add_help('weblcms_user_details');
    }

    function  get_additional_parameters()
    {
        return array(UserTool::PARAM_USERS);
    }

}

?>
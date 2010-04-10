<?php
/**
 * $Id: admin_user_browser_table_cell_renderer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component.admin_user_browser
 */
require_once dirname(__FILE__) . '/admin_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../user_table/default_user_table_cell_renderer.class.php';
/**
 * Cell renderer for the user object browser table
 */
class AdminUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
    /**
     * The user browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function AdminUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === AdminUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }

        // Add special features here
        switch ($column->get_name())
        {
            case User :: PROPERTY_OFFICIAL_CODE :
                return $user->get_official_code();
            // Exceptions that need post-processing go here ...
            case User :: PROPERTY_STATUS :
               /* if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('PlatformAdministrator');
                }*/
                if ($user->get_status() == '1')
                {
                    return Translation :: get('CourseAdmin');
                }
                else
                {
                    return Translation :: get('Student');
                }
            case User :: PROPERTY_PLATFORMADMIN :
            	return Utilities :: display_true_false_icon($user->get_platformadmin());
            case User :: PROPERTY_ACTIVE:
            	return Utilities :: display_true_false_icon($user->get_active());
        }
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     * @param $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($user)
    {
        $toolbar_data = array();

        $toolbar_data[] = array('href' => $this->browser->get_user_editing_url($user), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');

        $toolbar_data[] = array('href' => $this->browser->get_user_quota_url($user), 'label' => Translation :: get('VersionQuota'), 'img' => Theme :: get_common_image_path() . 'action_statistics.png');

        $toolbar_data[] = array('href' => $this->browser->get_manage_user_rights_url($user), 'label' => Translation :: get('ManageRightsTemplates'), 'img' => Theme :: get_common_image_path() . 'action_rights.png');

        $params = array();
        //$params[ReportingManager :: PARAM_APPLICATION] = "weblcms";
        //$params[ReportingManager :: PARAM_COURSE_ID] = $this->browser->get_course_id();
        $params[ReportingManager :: PARAM_USER_ID] = $user->get_id();
        //$url = ReportingManager :: get_reporting_template_registration_url_content($this->browser,'UserReportingTemplate',$params);
        //$url =

		//$unsubscribe_url = $this->browser->get_url($parameters);

		/*$toolbar_data[] = array(
			'href' => $this->browser->get_reporting_url('UserReportingTemplate',$params),
			'label' => Translation :: get('Report'),
			'img' => Theme :: get_common_image_path().'action_reporting.png'
		);*/

		$toolbar_data[] = array(
				'href' => $this->browser->get_user_detail_url($user->get_id()),
				'label' => Translation :: get('Detail'),
				'img' => Theme :: get_common_image_path().'action_details.png'
			);

        //$unsubscribe_url = $this->browser->get_url($parameters);
        $toolbar_data[] = array('href' => $this->browser->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_REPORTING, UserManager::PARAM_USER_USER_ID => $user->get_id())), 'label' => Translation :: get('Report'), 'img' => Theme :: get_common_image_path() . 'action_reporting.png');
        $toolbar_data[] = array('href' => $this->browser->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_VIEW_QUOTA, UserManager::PARAM_USER_USER_ID => $user->get_id())), 'label' => Translation :: get('ViewQuota'), 'img' => Theme :: get_common_image_path() . 'action_browser.png');

    	if(PlatformSetting :: get('active_online_email_editor'))
        {
        	$toolbar_data[] = array('href' => $this->browser->get_email_user_url($user), 'label' => Translation :: get('SendEmail'), 'img' => Theme :: get_common_image_path() . 'action_email.png');
        }

        if ($user->get_id() != Session :: get_user_id())
        {
            if (UserDataManager :: user_deletion_allowed($user))
            {
                $toolbar_data[] = array('href' => $this->browser->get_user_delete_url($user), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        	}

            $toolbar_data[] = array('href' => $this->browser->get_change_user_url($user), 'label' => Translation :: get('LoginAsUser'), 'img' => Theme :: get_common_image_path() . 'action_login.png');
        }


        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
<?php
/**
 * $Id: subscribed_user_browser_table_cell_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.subscribe_user_browser
 */
require_once dirname(__FILE__) . '/subscribed_user_browser_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscribedUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
    /**
     * The weblcms browser component
     */
    private $browser;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function SubscribedUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === SubscribedUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }

        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case User :: PROPERTY_STATUS :
                $course_user_relation = $this->browser->get_parent()->retrieve_course_user_relation($this->browser->get_course_id(), $user->get_id());
                if ($course_user_relation && $course_user_relation->get_status() == 1)
                {
                    return Translation :: get('CourseAdmin');
                }
                else
                {
                    return Translation :: get('Student');
                }
            case User :: PROPERTY_PLATFORMADMIN :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('PlatformAdministrator');
                }
                else
                {
                    return '';
                }
            case User :: PROPERTY_EMAIL :
                return '<a href="mailto:' . $user->get_email() . '">' . $user->get_email() . '</a>';
        }
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     * @param User $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($user)
    {
        $toolbar_data = array();
        if (Request :: get(WeblcmsManager :: PARAM_TOOL_ACTION) == WeblcmsManager :: ACTION_SUBSCRIBE)
        {
            $parameters = array();
            $parameters[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_SUBSCRIBE;
            $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
            $subscribe_url = $this->browser->get_url($parameters);
            $toolbar_data[] = array('href' => $subscribe_url, 'label' => Translation :: get('SubscribeAsStudent'), 'img' => Theme :: get_image_path() . 'action_subscribe_student.png');

            $parameters = array();
            $parameters[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_SUBSCRIBE;
            $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
            $parameters[WeblcmsManager :: PARAM_STATUS] = 1;
            $subscribe_url = $this->browser->get_url($parameters);
            $toolbar_data[] = array('href' => $subscribe_url, 'label' => Translation :: get('SubscribeAsTeacher'), 'img' => Theme :: get_image_path() . 'action_subscribe_teacher.png');
        }
        else
        {
            $parameters = array();
            $parameters[WeblcmsManager :: PARAM_TOOL_ACTION] = UserTool :: ACTION_USER_DETAILS;
            $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
            $unsubscribe_url = $this->browser->get_url($parameters);
            $toolbar_data[] = array('href' => $unsubscribe_url, 'label' => Translation :: get('Details'), 'img' => Theme :: get_common_image_path() . 'action_details.png');

            if(PlatformSetting :: get('active_online_email_editor'))
            {
	            $parameters = array();
	            $parameters[WeblcmsManager :: PARAM_TOOL_ACTION] = UserTool :: ACTION_EMAIL;
	            $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
	            $unsubscribe_url = $this->browser->get_url($parameters);
	            $toolbar_data[] = array('href' => $unsubscribe_url, 'label' => Translation :: get('Email'), 'img' => Theme :: get_common_image_path() . 'action_email.png');
            }

            $group_id = Request :: get(WeblcmsManager :: PARAM_GROUP);

            if ($user->get_id() != $this->browser->get_user()->get_id() && $this->browser->is_allowed(DELETE_RIGHT) && !isset($group_id))
            {
                $parameters = array();
                $parameters[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_UNSUBSCRIBE;
                $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
                $unsubscribe_url = $this->browser->get_url($parameters);
                $toolbar_data[] = array('href' => $unsubscribe_url, 'label' => Translation :: get('Unsubscribe'), 'img' => Theme :: get_image_path() . 'action_unsubscribe.png');
            }
            else
            {
                $toolbar_data[] = array('label' => Translation :: get('UnsubscribeNotAvailable'), 'img' => Theme :: get_image_path() . 'action_unsubscribe_na.png');
            }

            if ($this->browser->is_allowed(EDIT_RIGHT))
            {
                //@todo check rights ?
                //$parameters[WeblcmsManager :: PARAM_TOOL_ACTION] = UserTool::ACTION_USER_DETAILS;
                //$parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
                $params = array();
                //$params[ReportingManager :: PARAM_APPLICATION] = "weblcms";
                $params[WeblcmsManager :: PARAM_COURSE] = $this->browser->get_course_id();
                $params[WeblcmsManager::PARAM_USERS] = $user->get_id();
				$params[Application::PARAM_ACTION] = WeblcmsManager::ACTION_REPORTING;
				//$params[ReportingManager::PARAM_TEMPLATE_ID] = Reporting::get_name_registration(Utilities::camelcase_to_underscores('CourseStudentTrackerDetailReportingTemplate'), WeblcmsManager::APPLICATION_NAME)->get_id();
   
                $reporting_url = $this->browser->get_url($params, array(WeblcmsManager::PARAM_TOOL));
                //$unsubscribe_url = $this->browser->get_url($parameters);
                $toolbar_data[] = array('href' => $reporting_url, 'label' => Translation :: get('Report'), 'img' => Theme :: get_common_image_path() . 'action_reporting.png');
            }
        }
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
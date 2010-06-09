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
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        if (Request :: get(WeblcmsManager :: PARAM_TOOL_ACTION) == WeblcmsManager :: ACTION_SUBSCRIBE)
        {
            $parameters = array();
            $parameters[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_SUBSCRIBE;
            $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
            $subscribe_url = $this->browser->get_url($parameters);

            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('SubscribeAsStudent'),
	        		Theme :: get_common_image_path() . 'action_subscribe_student.png',
	        		$subscribe_url,
	        		ToolbarItem :: DISPLAY_ICON
	        ));
            
            $parameters = array();
            $parameters[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_SUBSCRIBE;
            $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
            $parameters[WeblcmsManager :: PARAM_STATUS] = 1;
            $subscribe_url = $this->browser->get_url($parameters);
            
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('SubscribeAsTeacher'),
	        		Theme :: get_common_image_path() . 'action_subscribe_teacher.png',
	        		$subscribe_url,
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }
        else
        {
            $parameters = array();
            $parameters[WeblcmsManager :: PARAM_TOOL_ACTION] = UserTool :: ACTION_USER_DETAILS;
            $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
            $details_url = $this->browser->get_url($parameters);
            
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Details'),
	        		Theme :: get_common_image_path() . 'action_details.png',
	        		$details_url,
	        		ToolbarItem :: DISPLAY_ICON
	        ));

            if(PlatformSetting :: get('active_online_email_editor'))
            {
	            $parameters = array();
	            $parameters[WeblcmsManager :: PARAM_TOOL_ACTION] = UserTool :: ACTION_EMAIL;
	            $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
	            $email_url = $this->browser->get_url($parameters);
	            
	            $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Email'),
		        		Theme :: get_common_image_path() . 'action_email.png',
		        		$email_url,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }

            $group_id = Request :: get(WeblcmsManager :: PARAM_GROUP);

            if ($user->get_id() != $this->browser->get_user()->get_id() && $this->browser->is_allowed(DELETE_RIGHT) && !isset($group_id))
            {
                $parameters = array();
                $parameters[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_UNSUBSCRIBE;
                $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
                $unsubscribe_url = $this->browser->get_url($parameters);
                
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Unsubscribe'),
		        		Theme :: get_common_image_path() . 'action_unsubscribe.png',
		        		$unsubscribe_url,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('UnsubscribeNotAvailable'),
		        		Theme :: get_common_image_path() . 'action_unsubscribe_na.png',
		        		null,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }

            if ($this->browser->is_allowed(EDIT_RIGHT))
            {
                $params = array();
                $params[WeblcmsManager :: PARAM_COURSE] = $this->browser->get_course_id();
                $params[WeblcmsManager::PARAM_USERS] = $user->get_id();
				$params[Application::PARAM_ACTION] = WeblcmsManager::ACTION_REPORTING;
                $reporting_url = $this->browser->get_url($params, array(WeblcmsManager::PARAM_TOOL));
                
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Report'),
		        		Theme :: get_common_image_path() . 'action_reporting.png',
		        		$reporting_url,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
        }
        return $toolbar->as_html();
    }
}
?>
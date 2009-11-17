<?php
/**
 * $Id: course_group_unsubscribed_user_browser_table_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';

class CourseGroupUnsubscribedUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
    private $browser;

    function CourseGroupUnsubscribedUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === CourseGroupUnsubscribedUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
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
        $parameters = array();
        $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
        $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_SUBSCRIBE;
        $unsubscribe_url = $this->browser->get_url($parameters);
        $toolbar_data[] = array('href' => $unsubscribe_url, 'label' => Translation :: get('Subscribe'), 'img' => Theme :: get_common_image_path() . 'action_subscribe.png');
        $parameters = array();
        /*$parameters[WeblcmsManager :: PARAM_TOOL_ACTION] = UserTool::USER_DETAILS;
			$parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
			$unsubscribe_url = $this->browser->get_url($parameters);
			$toolbar_data[] = array(
				'href' => $unsubscribe_url,
				'label' => Translation :: get('Details'),
				'img' => Theme :: get_common_image_path().'action_details.png'
			);*/
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
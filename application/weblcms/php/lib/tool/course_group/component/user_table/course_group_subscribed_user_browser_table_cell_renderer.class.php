<?php
/**
 * $Id: course_group_subscribed_user_browser_table_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';

class CourseGroupSubscribedUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
    private $browser;

    function CourseGroupSubscribedUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === CourseGroupSubscribedUserBrowserTableColumnModel :: get_modification_column())
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
    	$toolbar = new Toolbar();
        if($this->browser->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $parameters = array();
            $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_UNSUBSCRIBE;
            $parameters[WeblcmsManager :: PARAM_USERS] = $user->get_id();
            $parameters[CourseGroupTool :: PARAM_COURSE_GROUP] = $this->browser->get_course_group()->get_id();
            $unsubscribe_url = $this->browser->get_url($parameters);
            $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_unsubscribe.png', $unsubscribe_url, ToolbarItem::DISPLAY_ICON ));
        }
        
        $course_group = $this->browser->get_course_group();
        
    	if (!$this->browser->is_allowed(WeblcmsRights :: EDIT_RIGHT) && $course_group->is_self_unregistration_allowed() && $course_group->is_member($user) && $this->browser->get_user()->get_id() == $user->get_id())
        {
            $parameters = array();
            $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_USER_SELF_UNSUBSCRIBE;
            $unsubscribe_url = $this->browser->get_url($parameters);
            $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_unsubscribe.png', $unsubscribe_url, ToolbarItem::DISPLAY_ICON ));
        }
        
        return $toolbar->as_html();
    }
}
?>
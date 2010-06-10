<?php
/**
 * $Id: default_course_group_table_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.course_group_table
 */
require_once dirname(__FILE__) . '/course_group_table_cell_renderer.class.php';

class DefaultCourseGroupTableCellRenderer implements CourseGroupTableCellRenderer
{
    private $course_group_tool;

    /**
     * Constructor
     */
    function DefaultCourseGroupTableCellRenderer($course_group_tool)
    {
        $this->course_group_tool = $course_group_tool;
    }

    /**
     * Renders a table cell
     * @param CourseGroupTableColumnModel $column The column which should be rendered
     * @param CourseGroup $course_group The User to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $course_group)
    {
        if ($column === DefaultCourseGroupTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($course_group);
        }
        if ($column === DefaultCourseGroupTableColumnModel :: get_number_of_members_column())
        {
            if (! is_null($course_group->get_members()))
            {
                return $course_group->get_members()->size();
            }
            else
            {
                return '0';
            }
        }
        if ($property = $column->get_course_group_property())
        {
            switch ($property)
            {
                case CourseGroup :: PROPERTY_ID :
                    return $course_group->get_id();
                case CourseGroup :: PROPERTY_NAME :
                    if ($this->course_group_tool->is_allowed(EDIT_RIGHT) || $course_group->is_member($this->course_group_tool->get_user()))
                    {
                        $url = $this->course_group_tool->get_url(array(CourseGroupTool :: PARAM_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE, WeblcmsManager :: PARAM_COURSE_GROUP => $course_group->get_id()));
                        return '<a href="' . $url . '">' . $course_group->get_name() . '</a>';
                    }
                    else
                    {
                        return $course_group->get_name();
                    }
                case CourseGroup :: PROPERTY_DESCRIPTION :
                    return strip_tags($course_group->get_description());
                case CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS :
                    return $course_group->get_max_number_of_members();
                case CourseGroup :: PROPERTY_SELF_REG :
                    return $course_group->is_self_registration_allowed() ? Translation :: get('Yes') : Translation :: get('No');
                case CourseGroup :: PROPERTY_SELF_UNREG :
                    return $course_group->is_self_unregistration_allowed() ? Translation :: get('Yes') : Translation :: get('No');
            }
        }
        return '&nbsp;';
    }

    /**
     * Gets the action links to display
     * @param CourseGroup $course_group The course_group for which the action links should be
     * returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($course_group)
    {
        $toolbar = new Toolbar();
        $parameters = array();
        $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
        $details_url = $this->course_group_tool->get_url($parameters);
        // Default functionity achieved by clicking the course_group name, why add it as an icon ?
        //$toolbar_data[] = array ('href' => $details_url, 'label' => Translation :: get('Details'), 'img' => Theme :: get_common_image_path().'description.png');
        if ($this->course_group_tool->is_allowed(EDIT_RIGHT))
        {
            $parameters = array();
            $parameters[CourseGroupTool :: PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_EDIT_COURSE_GROUP;
            $edit_url = $this->course_group_tool->get_url($parameters);
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $edit_url, ToolbarItem::DISPLAY_ICON ));
            
            $parameters = array();
            $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_DELETE_COURSE_GROUP;
            $delete_url = $this->course_group_tool->get_url($parameters);
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $delete_url, ToolbarItem::DISPLAY_ICON, true ));
        }
        
        $user = $this->course_group_tool->get_user();
        
        if (!$this->course_group_tool->is_allowed(EDIT_RIGHT))
        {
        	if($course_group->is_self_registration_allowed())
	        {
	            if (! $course_group->is_member($user))
	            {
	                $parameters = array();
	                $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
	                $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_USER_SELF_SUBSCRIBE;
	                $subscribe_url = $this->course_group_tool->get_url($parameters);
	                $toolbar->add_item(new ToolbarItem(Translation :: get('Subscribe'), Theme :: get_common_image_path() . 'action_subscribe.png', $subscribe_url, ToolbarItem::DISPLAY_ICON ));

	            }
	        }
        }
        else
        {
            $parameters = array();
            $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_MANAGE_SUBSCRIPTIONS;
            $subscribe_url = $this->course_group_tool->get_url($parameters);
            $toolbar->add_item(new ToolbarItem(Translation :: get('$subscribe_url'), Theme :: get_common_image_path() . 'action_subscribe.png', $subscribe_url, ToolbarItem::DISPLAY_ICON ));
        }
        
        if (!$this->course_group_tool->is_allowed(EDIT_RIGHT) && $course_group->is_self_unregistration_allowed() && $course_group->is_member($user))
        {
            $parameters = array();
            $parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[CourseGroupTool :: PARAM_COURSE_GROUP_ACTION] = CourseGroupTool :: ACTION_USER_SELF_UNSUBSCRIBE;
            $unsubscribe_url = $this->course_group_tool->get_url($parameters);
            $toolbar->add_item(new ToolbarItem(Translation :: get('$Unsubscribe'), Theme :: get_common_image_path() . 'action_unsubscribe.png', $unsubscribe_url, ToolbarItem::DISPLAY_ICON ));
        }
        /*else
		{
			$parameters = array ();
			$parameters[WeblcmsManager :: PARAM_COURSE_GROUP] = $course_group->get_id();
			$parameters[CourseGroupTool::PARAM_COURSE_GROUP_ACTION] = CourseGroupTool::ACTION_UNSUBSCRIBE;
			$unsubscribe_url = $this->course_group_tool->get_url($parameters);
			$toolbar_data[] = array ('href' => $unsubscribe_url, 'label' => Translation :: get('UnsubscribeUsers'), 'img' => Theme :: get_common_image_path().'action_unsubscribe.png');
		}*/
        
        return $toolbar->as_html();
    }
}
?>
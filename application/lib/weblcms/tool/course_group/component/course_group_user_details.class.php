<?php
/**
 * $Id: course_group_user_details.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';

class CourseGroupToolCreatorComponent extends CourseGroupToolComponent
{
    private $action_bar;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses group');
        $this->display_header($trail, true);
        
        $this->display_footer();
    }

}
?>
<?php
/**
 * $Id: course_group_deleter.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component
 */
require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../course_group/course_group.class.php';

class CourseGroupToolDeleterComponent extends CourseGroupToolComponent
{

    function run()
    {
        if (! $this->is_allowed(DELETE_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $ids = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);
        if ($ids)
        {
            if (! is_array($ids))
                $ids = array($ids);
            
            $wdm = WeblcmsDataManager :: get_instance();
                
            foreach ($ids as $group_id)
            {
                $cg = $wdm->retrieve_course_group($group_id); 
                $cg->delete();
            }
            
            $message = Translation :: get('CourseGroupsDeleted');
            $this->redirect($message, '', array('course_group' => null, CourseGroupTool :: PARAM_ACTION => null));
        
        }
        else
        {
            Display :: display_error_message('NoObjectSelected');
        }
    
    }

}
?>
<?php
/**
 * $Id: course_viewer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

/**
 * Weblcms component which provides the course page
 */
class WeblcmsManagerCourseViewerComponent extends WeblcmsManager implements DelegateComponent
{
    private $rights;
    
    /**
     * The tools that this application offers.
     */
    private $tools;
    /**
     * The class of the tool currently active in this application
     */
    private $tool_class;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        //$this->load_rights();
        

        if ($this->is_teacher())
        {
            $studentview = Request :: get('studentview');
            if (is_null($studentview))
            {
                $studentview = Session :: retrieve('studentview');
            }
            
            if ($studentview == 1)
            {
                Session :: register('studentview', 1);
            }
            else
            {
                Session :: unregister('studentview');
            }
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        $tool_action = Request :: get(Tool :: PARAM_ACTION);
        
        if ($this->is_teacher() && $this->get_course()->get_student_view() == 1 && ! isset($tool_action))
        {
            $studentview = Session :: retrieve('studentview');
            
            if ($studentview == 1)
            {
                $trail->add_extra(new ToolbarItem(Translation :: get('TeacherView'), Theme :: get_image_path() . 'action_teacher_view.png', $this->get_url(array(WeblcmsManager :: PARAM_TOOL => Request :: get(WeblcmsManager :: PARAM_TOOL), 'studentview' => '0', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            else
            {
                $trail->add_extra(new ToolbarItem(Translation :: get('StudentView'), Theme :: get_image_path() . 'action_student_view.png', $this->get_url(array(WeblcmsManager :: PARAM_TOOL => Request :: get(WeblcmsManager :: PARAM_TOOL), 'studentview' => '1', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        
        if (! $this->is_teacher() && (! $this->is_subscribed($this->get_course(), $this->get_user()) || ! $this->get_course()->get_access()))
        {
            $this->display_header($trail, false, true, false);
            Display :: error_message(Translation :: get("NotAllowedToView"));
            $this->display_footer();
            exit();
        }
        
        if (! $this->is_course())
        {
            $this->display_header($trail, false, true, false);
            Display :: error_message(Translation :: get("NotACourse"));
            $this->display_footer();
            exit();
        }
        
        if ($studentview && $this->get_course()->get_student_view() != 1)
        {
            if ($this->is_teacher())
                $this->redirect(Translation :: get('StudentViewNotAvailable'), true, array('studentview' => 0));
            
            $this->display_header($trail, false, false, false);
            Display :: error_message(Translation :: get("StudentViewNotAvailable"));
            $this->display_footer();
            exit();
        }
        
        $this->load_course_theme();
        $this->load_course_language();
        
        /**
         * Here we set the rights depending on the user status in the course.
         * This completely ignores the roles-rights library.
         * TODO: WORK NEEDED FOR PROPPER ROLES-RIGHTS LIBRARY
         */
        
        $user = $this->get_user();
        $course = $this->get_course();
        if ($user != null && $course != null)
            $relation = $this->retrieve_course_user_relation($course->get_id(), $user->get_id());
            
        /*if(!$user->is_platform_admin() && (!$relation || ($relation->get_status() != 5 && $relation->get_status() != 1)))
		 //TODO: Roles & Rights
		 //if(!$this->is_allowed(WeblcmsRights :: VIEW_RIGHT) && !$this->get_user()->is_platform_admin())
		 {
			$this->display_header($trail, false, true);
			Display :: not_allowed();
			$this->display_footer();
			exit;
			}*/
        
        $course = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $tool = Request :: get(WeblcmsManager :: PARAM_TOOL);
        if (! $tool)
        {
            $tool = 'home';
        }
        
        $action = Request :: get(Application :: PARAM_ACTION);
        $category = Request :: get(WeblcmsManager :: PARAM_CATEGORY);
        
        if (! $category)
        {
            $category = 0;
        }
        
        if ($course)
        {
            if ($tool)
            {
                $title = CourseLayout :: get_title($this->get_course());
                
                if ($tool != 'course_group')
                {
                    $this->set_parameter('course_group', null);
                }
                
                $this->set_parameter(self :: PARAM_TOOL, $tool);
                
                if ($tool != 'home')
                {
                    //$trail->add(new Breadcrumb($this->get_url(), Translation :: get(Utilities :: underscores_to_camelcase($tool) . 'Title')));
                }
                
                $wdm = WeblcmsDataManager :: get_instance();
                $class = Tool :: type_to_class($tool);
                
                $this->set_tool_class($class);
                $wdm->log_course_module_access($this->get_course_id(), $this->get_user_id(), $tool, $category);
                
                Tool :: launch($tool, $this);
            }
        }
        else
        {
            Display :: header(Translation :: get('MyCourses'), 'Mycourses');
            $this->display_footer();
        }
    }

    function is_course()
    {
        return ($this->get_course()->get_id() != null ? true : false);
    }

    function load_course_theme()
    {
        $course_can_have_theme = $this->get_platform_setting('allow_course_theme_selection');
        $course = $this->get_course();
        
        if ($course_can_have_theme && $course->has_theme())
        {
            Theme :: set_theme($course->get_theme());
        }
    }

    function load_course_language()
    {
        $course = $this->get_course();
        Translation :: set_language($course->get_language());
    }
    
    private $is_teacher;

    function is_teacher()
    {
        if (is_null($this->is_teacher))
        {
            $user = $this->get_user();
            $course = $this->get_course();
            
            $this->is_teacher = parent :: is_teacher($course, $user);
        }
        
        return $this->is_teacher;
    }
    
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $title = CourseLayout :: get_title($this->get_course());
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_CATEGORY => null , WeblcmsManager :: PARAM_ACTION => Request :: get(WeblcmsManager :: PARAM_ACTION), WeblcmsManager :: PARAM_COURSE => Request :: get(WeblcmsManager :: PARAM_COURSE))), $title));

    }

    function get_additional_parameters()
    {
    	return array(self :: PARAM_COURSE);
    }
}
?>
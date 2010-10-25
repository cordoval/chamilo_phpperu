<?php
/**
 * $Id: course_import_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/course.class.php';
require_once dirname(__FILE__) . '/../category_manager/course_category.class.php';

ini_set("max_execution_time", - 1);
ini_set("memory_limit", - 1);

class CourseImportForm extends FormValidator
{
    const TYPE_IMPORT = 1;
    
    private $failedcsv;
    private $udm;

    function CourseImportForm($form_type, $action)
    {
        parent :: __construct('course_import', 'post', $action);
        
        $this->form_type = $form_type;
        $this->failedcsv = array();
        if ($this->form_type == self :: TYPE_IMPORT)
        {
            $this->build_importing_form();
        }
    }

    function build_importing_form()
    {
        $this->addElement('file', 'file', Translation :: get('FileName'));
        //$this->addElement('submit', 'course_import', Translation :: get('Ok'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));
        //	$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function import_courses()
    {
        $csvcourses = Import :: csv_to_array($_FILES['file']['tmp_name']);
        $failures = 0;
        
        foreach ($csvcourses as $csvcourse)
        {      
            if (!$this->validate_data($csvcourse))
            {       
                $failures ++;
                $this->failedcsv[] = Translation :: get('Invalid') . ': ' . implode($csvcourse, ';');
            }
        }
        
    	if ($failures > 0)
        {
            return false;
        }
        
        $wdm = WeblcmsDataManager :: get_instance();
        
        foreach($csvcourses as $csvcourse)
        {
        	$teacher_info = $this->get_teacher_info($csvcourse['teacher']);
            $cat = $wdm->retrieve_course_categories(new EqualityCondition('name', $csvcourse['category']))->next_result();
            $catid = $cat ? $cat->get_id() : 0;
            $action = strtoupper($csvcourse['action']);
            
            if($action == 'A')
            {
           		$course = new Course();

	            $course->set_visual($csvcourse['code']);
	            $course->set_name($csvcourse[Course :: PROPERTY_NAME]);
	            $course->set_category($catid);
	            $course->set_titular($teacher_info->get_id());
	            $course->set_language('english');
	            
	            if ($course->create())
	            {
	                $wdm = WeblcmsDataManager :: get_instance();
		            if (!$wdm->subscribe_user_to_course($course, '1', '1', $teacher_info->get_id()))
		            {
		              	$failures ++;
		                $this->failedcsv[] = Translation :: get('SubscriptionFailed') . ':' . implode($csvcourse, ';');
		            }
	            }
	            else
	            {
	                $failures ++;
	                $this->failedcsv[] = Translation :: get('CreationFailed') . ':' . implode($csvcourse, ';');
	            }
            }
            elseif($action == 'U')
            {
            	$course = $wdm->retrieve_courses(new EqualityCondition(Course :: PROPERTY_VISUAL, $csvcourse['code']))->next_result();;
	            $course->set_name($csvcourse[Course :: PROPERTY_NAME]);
	            //$course->set_language('english');
	            $course->set_category($catid);
	            $course->set_titular($teacher_info->get_id());
	            if (!$course->update())
	            {
	            	$failures ++;
	                $this->failedcsv[] = Translation :: get('UpdateFailed') . ':' . implode($csvcourse, ';');
	            }
            }
            elseif($action == 'D')
            {
            	$course = $wdm->retrieve_courses(new EqualityCondition(Course :: PROPERTY_VISUAL, $csvcourse['code']))->next_result();
            
           		if (!$wdm->delete_course($course->get_id()))
	            {
	            	$failures ++;
	                $this->failedcsv[] = Translation :: get('DeleteFailed') . ':' . implode($csvcourse, ';');
	            }
            }
        }
        
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    // TODO: Temporary solution pending implementation of user object
    function get_teacher_info($user_name)
    {
        $udm = UserDataManager :: get_instance();
        if (! $udm->is_username_available($user_name))
        {
            return $udm->retrieve_user_info($user_name);
        }
        else
        {
            return null;
        }
    }

    function get_failed_csv()
    {
        return implode($this->failedcsv, '<br />');
    }

    function validate_data($csvcourse)
    {
        $failures = 0;
        
	    //1. Action valid ?
	    $action = strtoupper($csvcourse['action']);
        if($action != 'A' && $action != 'D' && $action != 'U')
        {
        	$failures++; 
        }

        //2. check if code isn't in use for create and if code exists for update / delete
        if ( ($action == 'A' && $this->is_course($csvcourse['code'])) || 
        	 ($action != 'A' && !$this->is_course($csvcourse['code']) ))
		{
			$failures++;
		}
        
        if ($csvcourse['teacher'])
        {
            $csvcourse[Course :: PROPERTY_TITULAR] = $csvcourse['teacher'];
        }
        
        //3. check if teacher exists
        $teacher_info = $this->get_teacher_info($csvcourse[Course :: PROPERTY_TITULAR]);
        if (! isset($teacher_info))
        {
            $failures ++;
        }
        
        //4. check if category exists
        if (! $this->is_course_category($csvcourse['category']))
        {
            $failures ++;
        }
        
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    private function is_course_category($category_name)
    {
        $cat = WeblcmsDataManager :: get_instance()->retrieve_course_categories(new EqualityCondition('name', $category_name))->next_result();
        if ($cat)
        {
            return true;
        }
        
        return false;
    }
    
    private function is_course($course_code)
    {
    	$course = WeblcmsDataManager :: get_instance()->retrieve_courses(new EqualityCondition(Course :: PROPERTY_VISUAL, $course_code))->next_result();
        if ($course)
        {
            return true;
        }
        
        return false;
    }
}
?>
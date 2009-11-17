<?php
/**
 * $Id: course_user_import_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.course
 */
require_once dirname(__FILE__) . '/course.class.php';
require_once dirname(__FILE__) . '/course_user_relation.class.php';

ini_set("max_execution_time", - 1);
ini_set("memory_limit", - 1);

class CourseUserImportForm extends FormValidator
{
    
    const TYPE_IMPORT = 1;
    
    private $failedcsv;

    function CourseUserImportForm($form_type, $action)
    {
        parent :: __construct('course_user_import', 'post', $action);
        
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
        //$this->addElement('submit', 'course_user_import', Translation :: get('Ok'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Ok'), array('class' => 'positive'));
        //$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function import_course_users()
    {
        $course = $this->course;
        
        $csvcourses = Import :: csv_to_array($_FILES['file']['tmp_name']);
        $failures = 0;
        
        foreach ($csvcourses as $csvcourse)
        {
            if (!$this->validate_data($csvcourse))
            {
            	$failures ++;
                $this->failedcsv[] = implode($csvcourse, ';');
            }
        }
        
    	if ($failures > 0)
        {
            return false;
        }
        
        foreach($csvcourses as $csvcourse)
        {
       		$user_info = $this->get_user_info($csvcourse['username']);
                
            $code = $csvcourse['coursecode'];
            $course = WeblcmsDataManager :: get_instance()->retrieve_courses(new EqualityCondition('visual_code', $code))->next_result();
                
            $wdm = WeblcmsDataManager :: get_instance();
            if (! $wdm->subscribe_user_to_course($course, $csvcourse[CourseUserRelation :: PROPERTY_STATUS], ($csvcourse[CourseUserRelation :: PROPERTY_STATUS] == 1 ? 1 : 5), $user_info->get_id()))
            {
                $failures ++;
                $this->failedcsv[] = implode($csvcourse, ';');
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
    function get_user_info($user_name)
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
        
        //1. check if user exists
        // TODO: Change to appropriate property once the user-class is operational
        $user_info = $this->get_user_info($csvcourse['username']);
        if (! isset($user_info))
        {
            $failures ++;
        }
        
        if ($csvcourse['coursecode'])
        {
            $csvcourse['course'] = $csvcourse['coursecode'];
        }
        
        //2. check if course code exists
        if (! $this->is_course($csvcourse['course']))
        {
            $failures ++;
        }
        
        //3. Status valid ?
        if ($csvcourse[CourseUserRelation :: PROPERTY_STATUS] != 1 && $csvcourse[CourseUserRelation :: PROPERTY_STATUS] != 5)
        {
            $failures ++;
        }
        
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return $csvcourse;
        }
    }

    function is_course($course_code)
    {
        $course = WeblcmsDataManager :: get_instance()->retrieve_courses(new EqualityCondition('visual_code', $course_code))->next_result();
        
        if ($course)
            return true;
        
        return false;
    }
}
?>
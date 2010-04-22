<?php
/**
 * $Id: group_move_form.class.php 224 2010-04-06 14:40:30Z yannick $
 * @package applicatie.lib.weblcms.course
 */

require_once Path :: get_application_path() . 'lib/weblcms/course/course.class.php';

class CourseChangeCourseTypeForm extends FormValidator
{
    const SELECT_COURSE_TYPE = 'course_type';
    private $size;
    private $single_course_type_id;
    private $course;
    private $wdm;

    function CourseChangeCourseTypeForm($action,$course,$user)
    {
        parent :: __construct('course_change_course_type', 'post', $action);
        $this->course = $course;
        $this->allow_no_course_type = $user->is_platform_admin() || PlatformSetting::get('allow_course_creation_without_coursetype', 'weblcms');
        $this->wdm = WeblcmsDataManager :: get_instance();
        
        $this->build_form();
    }

    function build_form()
    {
    	$this->addElement('hidden', Course :: PROPERTY_ID);
    	
        $this->addElement('select', self :: SELECT_COURSE_TYPE, Translation :: get('NewCourseType'), $this->get_course_types());
        $this->addRule('CourseType', Translation :: get('ThisFieldIsRequired'), 'required');
                 
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('ChangeCourseType'), array('class' => 'positive move'));        

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
	
	function get_selected_course_type()
    {
        return $this->exportValue(self :: SELECT_COURSE_TYPE);
    }
    
    function get_course_types()
    {  	
    	$wdm = WeblcmsDataManager :: get_instance();
    	$course_type_objects = $wdm->retrieve_course_types();
        $course_types = array();
        if(empty($this->course_type_id) || $this->allow_no_course_type)
        	$course_types[0] = Translation :: get('NoCourseType');
        $this->size = $course_type_objects->size();
        if($this->size != 0)
        {
        	$count = 0;
        	while($course_type = $course_type_objects->next_result())
        		$course_types[$course_type->get_id()] = $course_type->get_name();
        			
        	if(is_null($this->course_type_id) && count == 0 && !$this->allow_no_course_type)
        		{
        			$parameters = array('go' => WeblcmsManager :: ACTION_COURSE_CHANGE_COURSETYPE, 'course' => $course->get_id());
        			$this->parent->simple_redirect($parameters);
        		}
        	$this->addElement('select', Course :: PROPERTY_ID,  Translation :: get('CourseType'), $course_types, array('class' => 'course_type_selector'));
        	$this->addRule('CourseType', Translation :: get('ThisFieldIsRequired'), 'required');
        }
        else
        {
        	$course_type_name = Translation :: get('NoCourseType');
       		if(!is_null($this->course_type_id))
       			$course_type_name = $this->object->get_course_type()->get_name();
       		$this->addElement('static', 'course_type', Translation :: get('CourseType'), $course_type_name);
        	$this->addElement('hidden', Course :: PROPERTY_ID);
        }
        return $course_types;        
    }
	
    function get_new_parent()
    {
        return $this->exportValue(self :: SELECT_COURSE_TYPE);
    }
    
	function get_selected_id()
	{		
		if($this->size!=1)
		{
			$values = $this->exportValues();
			return $values[self::SELECT_ELEMENT];
		}
		else
			return $this->single_course_type_id;
	}
	
	function get_size()
	{
		return $this->size;	
	}
}
?>
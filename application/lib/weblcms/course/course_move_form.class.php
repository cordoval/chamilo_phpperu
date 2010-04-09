<?php
/**
 * $Id: group_move_form.class.php 224 2010-04-06 14:40:30Z yannick $
 * @package applicatie.lib.weblcms.course
 */

require_once Path :: get_application_path() . 'lib/weblcms/course/course.class.php';

class CourseMoveForm extends FormValidator
{
    const SELECT_COURSE_TYPE = 'course_type';
    private $size;
    private $single_course_type_id;
    private $course;
    private $wdm;

    function CourseMoveForm($action,$course)
    {
        parent :: __construct('course_move', 'post', $action);
        $this->course = $course;
        
        $this->wdm = WeblcmsDataManager :: get_instance();
        
        $this->build_form();
    }

    function build_form()
    {
    	$this->addElement('hidden', Course :: PROPERTY_ID);
        $this->addElement('select', self :: SELECT_COURSE_TYPE, Translation :: get('New Course Type'), $this->get_course_types());
        $this->addRule('CourseType', Translation :: get('ThisFieldIsRequired'), 'required');
                 
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive move'));        

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
        $this->size = $course_type_objects->size();
        if($this->size == 1)
        {
        	$course_type = $course_type_objects->next_result();
        	$course_types[$course_type->get_id()] = $course_type->get_name();
        	$this->single_course_type_id = $course_type->get_id();
        }
        else
        {
        	while($course_type = $course_type_objects->next_result())
        		$course_types[$course_type->get_id()] = $course_type->get_name();
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
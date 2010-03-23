<?php
/**
 * $Id: course_type_form.class.php 2 2010-02-25 11:43:06Z Yannick & Tristan $
 * @package application.lib.weblcms.course_type
 */

class CourseTypeSelectForm extends FormValidator
{

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';

	private $size;
	
	function CourseTypeSelectForm($action)
	{
		parent :: __construct('course_type_select', 'post', $action);
		$this->build_form();
		$this->setDefaults();
	}

	function build_form()
	{
		$this->addElement('hidden', Course :: PROPERTY_ID);
		
        $wdm = WeblcmsDataManager :: get_instance();
		$course_type_objects = $wdm->retrieve_active_course_types();
        $course_types = array();
        $this->size = $course_type_objects->size();
        while($course_type = $course_type_objects->next_result())
        	$course_types[$course_type->get_id()] = $course_type->get_name();
        
       	$this->addElement('select', 'CourseType', Translation :: get('CourseType'), $course_types);
        $this->addRule('CourseType', Translation :: get('ThisFieldIsRequired'), 'required');
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Select'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}
	
	function get_selected_id()
	{
		$values = $this->exportValues();
		return $values['CourseType'];
	}
	
	function get_size()
	{
		return $this->size;	
	}

}
?>
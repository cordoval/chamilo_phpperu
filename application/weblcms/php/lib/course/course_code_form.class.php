<?php
/**
 * $Id: course_request_form.class.php 2 2010-02-25 11:43:06Z Yannick & Tristan $
 * @package application.lib.weblcms.course_type
 */
require_once dirname(__FILE__) . '/course.class.php';

class CourseCodeForm extends FormValidator
{	
	const TEMP_CODE = 'temp_code';
	private $form_type;
	private $course;
	private $parent;
	private $user;
	private $temp_code;

	function CourseCodeForm($action, $course, $parent, $user)
	{
		parent :: __construct('course_code', 'post', $action);
		$this->parent = $parent;
		$this->course = $course;
		$this->user = $user;
        $wdm = WeblcmsDataManager :: get_instance();

        $this->build_creating_form();       
	
        $this->setDefaults();
        $this->add_progress_bar(2);
	}
	
	function build_creating_form()
	{		
		$this->build_code_form();
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Subscribe'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);	
	}
	
	function build_code_form()
	{	
		$this->addElement('category', Translation :: get('CourseCodeProperties'));

		$course_name = $this->course->get_name();
     	$this->addElement('static', 'course', Translation :: get('Course'), $course_name);
		
		$user_name = $this->user->get_fullname();
		$this->addElement('static', 'user', Translation :: get('User'), $user_name);
     	
		$this->add_textfield(self :: TEMP_CODE, Translation :: get('Code'));
				
		$this->addElement('category');
	}	
	
	function check_code()
	{
		$temp_code = $this->exportValue(self::TEMP_CODE);
		$code = $this->course->get_code();
		
		if($temp_code == $code)
			return true;
		else
			return false;
	}
}
?>
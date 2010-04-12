<?php
/**
 * $Id: course_request_form.class.php 2 2010-02-25 11:43:06Z Yannick & Tristan $
 * @package application.lib.weblcms.course_type
 */
require_once dirname(__FILE__) . '/course_request.class.php';
require_once dirname(__FILE__) . '/course.class.php';

class CourseRequestForm extends FormValidator
{
	const TYPE_CREATE = 1;
	
	private $form_type;
	private $course;
	private $parent;

	function CourseRequestForm($form_type, $action, $course, $parent)
	{
		parent :: __construct('course_request', 'post', $action);
		$this->parent = $parent;
		$this->form_type = $form_type;
		$this->course = $course;
		//$course_object = $wdm->retrieve_course($course);   
        $wdm = WeblcmsDataManager :: get_instance();
        
		if ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creating_form();
        }
        
        //$this->setDefaults();
	}
	
	function build_creating_form()
	{		
		$this->build_request_form();
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}
	
	function build_request_form()
	{
		$this->addElement('hidden', Course :: PROPERTY_ID);
		
		$this->addElement('category', Translation :: get('CourseRequestDefaultProperties'));
		
     	//$course_name = "course";
     	$course_name = $this->course->get_name();
     	$this->addElement('static', 'course', Translation :: get('Course'), $course_name);
     	
		$this->add_textfield(CourseRequest :: PROPERTY_NAME_USER, Translation :: get('UserName'));
		$this->add_textfield(CourseRequest :: PROPERTY_TITLE, Translation :: get('Title'));
		$this->add_html_editor(CourseRequest :: PROPERTY_MOTIVATION, Translation :: get('Motivation'), true, array(FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR => 'BasicMarkup'));	
			
		$this->addElement('category');
	}
		
	function setDefaults($defaults = array ())
	{
		$course = $this->course;
		$course_request = $course;
		$defaults[CourseRequest :: PROPERTY_NAME_USER] = $course_request->get_name_user();
		$defaults[CourseRequest :: PROPERTY_TITLE] = $course_request->get_title();
		$defaults[CourseRequest :: PROPERTY_MOTIVATION] = $course_request->get_motivation();
		$defaults[CourseRequest :: PROPERTY_CREATIONDATE] = $course_request->get_creationdate();
		$defaults[CourseRequest :: PROPERTY_ALLOWEDDATE] = $course_request->get_alloweddate();
		
		parent :: setDefaults($defaults);
	}
	
	function get_form_type()
	{
		return $this->form_type;
	}
}
?>
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
	const TYPE_EDIT = 2;
	const TYPE_VIEW = 3;
	
	private $form_type;
	private $course;
	private $parent;
	private $request;
	private $user;

	function CourseRequestForm($form_type, $action, $course, $parent, $request, $user)
	{
		parent :: __construct('course_request', 'post', $action);
		$this->parent = $parent;
		$this->request = $request;
		$this->form_type = $form_type;
		$this->course = $course;
		$this->user = $user;
        $wdm = WeblcmsDataManager :: get_instance();
        
		if ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creating_form();
        }
		if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        if ($this->form_type == self :: TYPE_VIEW)
        {
        	$this->build_viewing_form();
        }
            
        $this->setDefaults();
        $this->add_progress_bar(2);
	}
	
	function build_creating_form()
	{		
		$this->build_request_form();
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);	
	}
	
	function build_editing_form()
    {
        $this->build_request_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    function build_viewing_form()
    {
   		$this->build_request_form();
   		
   		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Print'), array('class' => 'positive update'));
   		
   		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
	
	function build_request_form()
	{	
		$this->addElement('category', Translation :: get('CourseRequestProperties'));
		
		if ($this->form_type == self :: TYPE_CREATE)
		{
			$course_name = $this->course->get_name();
     		$this->addElement('static', 'course', Translation :: get('Course'), $course_name);
		
			$user_name = $this->user->get_fullname();
			$this->addElement('static', 'user', Translation :: get('User'), $user_name);
     	
			$this->add_textfield(CourseRequest :: PROPERTY_TITLE, Translation :: get('Title'),true);
				
			$this->add_html_editor(CourseRequest :: PROPERTY_MOTIVATION, Translation :: get('Motivation'), true, array(FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR => 'BasicMarkup'));			
		}
		
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$request_name = $this->request->get_course_name();
     		$this->addElement('static', 'request', Translation :: get('Course'), $request_name);
     		
     		$this->add_textfield(CourseRequest :: PROPERTY_TITLE, Translation :: get('Title'),true);
     		
     		$name_user = $this->request->get_name_user();
			$this->addElement('static', 'request', Translation :: get('User'), $name_user);
			
			$this->add_html_editor(CourseRequest :: PROPERTY_MOTIVATION, Translation :: get('Motivation'), true, array(FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR => 'BasicMarkup'));			
			
			$creation_date = $this->request->get_creation_date();
			$this->addElement('static', 'request', Translation :: get('CreationDate'), $creation_date);
			
			$this->add_datepicker(CourseRequest :: PROPERTY_ALLOWED_DATE, 'AllowedDate');
			$this->addRule(CourseRequest :: PROPERTY_ALLOWED_DATE, Translation :: get('ThisFieldIsRequired'), 'required');
		}
		
		if($this->form_type == self :: TYPE_VIEW)
		{
			$request_name = $this->request->get_course_name();
     		$this->addElement('static', 'request', Translation :: get('Course'), $request_name);
     		
     		$request_title = $this->request->get_title();
     		$this->addElement('static', 'request', Translation :: get('Course'), $request_title);
     		
     		$name_user = $this->request->get_name_user();
			$this->addElement('static', 'request', Translation :: get('User'), $name_user);
			
			$motivation = $this->request->get_motivation();
			$this->addElement('static','request', Translation :: get('Motivation'), $motivation);	
			
			$creation_date = $this->request->get_creation_date();
			$this->addElement('static', 'request', Translation :: get('CreationDate'), $creation_date);
			
			$allowed_date = $this->request->get_allowed_date();
			$this->addElement('static', 'request', Translation :: get('AllowedDate'), $allowed_date);
		}
		$this->addElement('category');
	}	
	
	function create_request()
    {		   	
        $values = $this->exportValues();
		
		$course = $this->course;
		$request = $this->request;
		$user = $this->user;
		
		$request->set_course_id($course->get_id());		
        $request->set_name_user($user->get_fullname());
        $request->set_course_name($course->get_name());
        $request->set_title($values[CourseRequest :: PROPERTY_TITLE]);
        $request->set_motivation($values[CourseRequest :: PROPERTY_MOTIVATION]);
        $request->set_creation_date(Utilities :: to_db_date(time()));
        $request->set_allowed_date($values[CourseRequest :: PROPERTY_ALLOWED_DATE]);
        
    	if(!$request->create())
			return false;

		return true;
    }
    
	function update_request($request_id)
	{
		$values = $this->exportValues();
		$request = $this->request;

        $request->set_course_id($request->get_course_id());	
        $request->set_name_user($request->get_name_user());
        $request->set_course_name($request->get_course_name());
        $request->set_title($values[CourseRequest :: PROPERTY_TITLE]);
        $request->set_motivation($values[CourseRequest :: PROPERTY_MOTIVATION]);
        $request->set_creation_date($request->get_creation_date());
        $request->set_allowed_date($values[CourseRequest :: PROPERTY_ALLOWED_DATE]);
	      
        if(!$request->update())
		{
			return false;
		}
		return true;		
	}

	function setDefaults($defaults = array ())
	{
		$course = $this->course;
		$request = $this->request;
		
		$defaults[CourseRequest :: PROPERTY_COURSE_ID] = $request->get_course_id();
		$defaults[CourseRequest :: PROPERTY_NAME_USER] = $request->get_name_user();
		$defaults[CourseRequest :: PROPERTY_COURSE_NAME] = $request->get_course_name();
		$defaults[CourseRequest :: PROPERTY_TITLE] = $request->get_title();
		$defaults[CourseRequest :: PROPERTY_MOTIVATION] = $request->get_motivation();
		$defaults[CourseRequest :: PROPERTY_CREATION_DATE] = $request->get_creation_date();
		$defaults[CourseRequest :: PROPERTY_ALLOWED_DATE] = $request->get_allowed_date();
		
		parent :: setDefaults($defaults);
	}
	
	function get_form_type()
	{
		return $this->form_type;
	}
}
?>
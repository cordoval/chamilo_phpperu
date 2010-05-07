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
	
	const CHOOSE_DATE = 'choose date';
	
	private $form_type;
	private $course;
	private $parent;
	private $request;
	private $user_id;

	function CourseRequestForm($form_type, $action, $course, $parent, $request)
	{
		parent :: __construct('course_request', 'post', $action);
		$this->parent = $parent;
		$this->request = $request;
		$this->form_type = $form_type;
		$this->course = $course;
		$this->user_id = $parent->get_user_id();
        $wdm = WeblcmsDataManager :: get_instance();
        
		if ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creating_form();
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
    
    function build_viewing_form()
    {
   		$this->build_request_form();
   		
   		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Print'), array('class' => 'positive update'));
   		
   		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
	
	function build_request_form()
	{	
		$this->addElement('html', '<div class="clear">&nbsp;</div><br/>');
		if ($this->form_type == self :: TYPE_CREATE)
		{
		
			$this->addElement('category', Translation :: get('CourseRequestProperties'));
			
			$user_name = UserDataManager::get_instance()->retrieve_user($this->user_id)->get_fullname();
			$this->addElement('static', 'user', Translation :: get('User'), $user_name);
			
			if(get_class($this->request) == "CourseCreateRequest")
			{        
				$wdm = WeblcmsDataManager :: get_instance();
				$course_type_objects = $wdm->retrieve_course_types_by_user_right($this->parent->get_user(), CourseTypeGroupCreationRight :: CREATE_REQUEST);
		        $course_types = array();
		        foreach($course_type_objects as $course_type)
		        	$course_types[$course_type->get_id()] = $course_type->get_name();
        		$this->addElement('select', CourseCreateRequest :: PROPERTY_COURSE_TYPE_ID,  Translation :: get('CourseType'), $course_types, array('class' => 'course_type_selector'));
        		$this->addRule(CourseCreateRequest :: PROPERTY_COURSE_TYPE_ID, Translation :: get('ThisFieldIsRequired'), 'required');
				$this->add_textfield(CourseCreateRequest :: PROPERTY_COURSE_NAME, Translation :: get('CourseName'),true);
				$this->addRule(CourseCreateRequest :: PROPERTY_COURSE_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
			}			
			else
			{
				$course_name = $this->course->get_name();
     			$this->addElement('static', 'course', Translation :: get('Course'), $course_name);
			}
			
			$this->add_textfield(CommonRequest :: PROPERTY_SUBJECT, Translation :: get('Subject'),true);
				
			$this->add_html_editor(CommonRequest :: PROPERTY_MOTIVATION, Translation :: get('Motivation'), true, array(FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR => 'BasicMarkup'));
						
		}
		
		if($this->form_type == self :: TYPE_VIEW)
		{
			$this->addElement('category', Translation :: get('CourseRequestProperties'));
     		
     		$name_user = UserDataManager::get_instance()->retrieve_user($this->request->get_user_id())->get_fullname();
			$this->addElement('static', 'request', Translation :: get('User'), $name_user);
			
			if(get_class($this->request) == 'CourseCreateRequest')
				$request_name = $this->request->get_course_name();		
			else
				$request_name = $this->parent->retrieve_course($this->request->get_course_id())->get_name();
     		
     		$this->addElement('static', 'request', Translation :: get('Course'), $request_name);
     		
     		$request_subject = $this->request->get_subject();
     		$this->addElement('static', 'request', Translation :: get('Subject'), $request_title);
			
			$motivation = $this->request->get_motivation();
			$this->addElement('static','request', Translation :: get('Motivation'), $motivation);	
			
			$creation_date = $this->request->get_creation_date();
			$this->addElement('static', 'request', Translation :: get('CreationDate'), $creation_date);
			
			$decision = $this->request->get_decision();
			$decision_date = $this->request->get_decision_date();
			switch($decision)
			{
				case CommonRequest :: ALLOWED_DECISION: $this->addElement('static', 'request', Translation :: get('Decision'), Translation :: get('Allowed') );
														$this->addElement('static', 'request', Translation :: get('on'), $decision_date);
														break;
				case CommonRequest :: ALLOWED_DECISION: $this->addElement('static', 'request', Translation :: get('Decision'), Translation :: get('Denied') );
														$this->addElement('static', 'request', Translation :: get('on'), $decision_date);
														break;
				default:  $this->addElement('static', 'request', Translation :: get('Decision'), Translation :: get('NoDecisionYet'));
						  break;
			}
		}
		$this->addElement('category');
	}	

	function create_request()
    {		   	
        $values = $this->exportValues();
		
		$course = $this->course;
		$request = $this->request;
		
		
		if(get_class($this->request) == "CourseCreateRequest")
		{
			$request->set_course_name($values[CourseCreateRequest :: PROPERTY_COURSE_NAME]);
			$request->set_course_type_id($values[CourseCreateRequest :: PROPERTY_COURSE_TYPE_ID]);
		}
		else
			$request->set_course_id($course->get_id());	
		$request->set_user_id($this->user_id);
        $request->set_subject($values[CommonRequest :: PROPERTY_SUBJECT]);
        $request->set_motivation($values[CommonRequest :: PROPERTY_MOTIVATION]);
        $request->set_creation_date(time());
        $request->set_decision_date($values[CommonRequest :: PROPERTY_DECISION_DATE]);
        $request->set_decision(CommonRequest :: NO_DECISION);	
        
    	if(!$request->create())
			return false;

		return true;
    }

	function setDefaults($defaults = array ())
	{
		$course = $this->course;
		$request = $this->request;
		$user = $this->user;
		
		if(get_class($this->request) == "CourseCreateRequest")
			$defaults[CourseCreateRequest :: PROPERTY_COURSE_NAME] = $request->get_course_name();
		else
			$defaults[CourseRequest :: PROPERTY_COURSE_ID] = $request->get_course_id();
		$defaults[CommonRequest :: PROPERTY_USER_ID] = $request->get_user_id();
		$defaults[CommonRequest :: PROPERTY_SUBJECT] = $request->get_subject();
		$defaults[CommonRequest :: PROPERTY_MOTIVATION] = $request->get_motivation();
		$defaults[CommonRequest :: PROPERTY_CREATION_DATE] = $request->get_creation_date();
		$defaults[CommonRequest :: PROPERTY_DECISION_DATE] = $request->get_decision_date();
		
		parent :: setDefaults($defaults);
	}
	
	function get_form_type()
	{
		return $this->form_type;
	}
}
?>
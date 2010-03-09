<?php
require_once Path :: get_library_path().'/html/formvalidator/form_validator.class.php';

class GradebookForm extends FormValidator {

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'GradebookUpdated';
	const RESULT_ERROR = 'GradebookUpdateFailed';
	
	private $gradebook;
	private $user;

	function GradebookForm($form_type, $gradebook, $action, $user) {
		parent :: __construct('gradebook_settings', 'post', $action);
	
		$this->gradebook = $gradebook;
		$this->user = $user;
		$this->form_type = $form_type;

		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}

		$this->setDefaults();
	}

	function build_basic_form()
	{
	
		$this->addElement('text', Gradebook :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
		$this->addRule(Gradebook :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->add_html_editor(Gradebook :: PROPERTY_DESCRIPTION, Translation :: get('Description'), true);
		$this->addElement('text', Gradebook :: PROPERTY_SCALE, Translation :: get('Scale'), array("size" => "4"));
		$this->addRule(Gradebook :: PROPERTY_SCALE, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->add_timewindow(Gradebook :: PROPERTY_START, Gradebook :: PROPERTY_END, Translation::get('Start'),Translation::get('End'), true);
		
	}

	function build_editing_form()
	{
		$registration = $this->registration;
		$parent = $this->parent;
		 
		$this->build_basic_form();
		 
		$this->addElement('hidden', Gradebook :: PROPERTY_ID);
		 
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}

	function build_creation_form()
	{
		$this->build_basic_form();
		 
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}

	function update_gradebook()
	{
		$gradebook = $this->gradebook;
		$values = $this->exportValues();
		 
		$gradebook->set_name($values[Gradebook :: PROPERTY_NAME]);
		$gradebook->set_description($values[Gradebook :: PROPERTY_DESCRIPTION]);
		$gradebook->set_scale($values[Gradebook :: PROPERTY_SCALE]);
		$start = Utilities :: time_from_datepicker($values[Gradebook :: PROPERTY_START]);
		$gradebook->set_start($start);
		$end = Utilities :: time_from_datepicker($values[Gradebook :: PROPERTY_END]);
		$gradebook->set_end($end);	
		
		$value = $gradebook->update();
			
		if($value)
		{
			//Events :: trigger_event('update', 'group', array('target_group_id' => $group->get_id(), 'action_user_id' => $this->user->get_id()));
		}
		 
		return $value;
	}

	function create_gradebook()
	{
		$gradebook = $this->gradebook;
		$values = $this->exportValues();

		$gradebook->set_name($values[Gradebook :: PROPERTY_NAME]);
		$gradebook->set_description($values[Gradebook :: PROPERTY_DESCRIPTION]);
		$gradebook->set_owner_id($this->user->get_id());
		$gradebook->set_scale($values[Gradebook :: PROPERTY_SCALE]);
		$start = Utilities :: time_from_datepicker($values[Gradebook :: PROPERTY_START]);
		$gradebook->set_start($start);
		$end = Utilities :: time_from_datepicker($values[Gradebook :: PROPERTY_END]);
		$gradebook->set_end($end);	
			 
		$value = $gradebook->create();

		if($value)
		{
			//Events :: trigger_event('create', 'group', array('target_group_id' => $group->get_id(), 'action_user_id' => $this->user->get_id()));
		}
		 
		return $value;
	}

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$gradebook = $this->gradebook;
		$defaults[Gradebook :: PROPERTY_ID] = $gradebook->get_id();
		$defaults[Gradebook :: PROPERTY_CREATED] = $gradebook->get_created();
		$defaults[Gradebook :: PROPERTY_DESCRIPTION] = $gradebook->get_description();
		$defaults[Gradebook :: PROPERTY_NAME] = $gradebook->get_name();
		$defaults[Gradebook :: PROPERTY_OWNER_ID] = $gradebook->get_owner_id();
		$defaults[Gradebook :: PROPERTY_SCALE] = $gradebook->get_scale();
		$defaults[Gradebook :: PROPERTY_START] = $gradebook->get_start();
		$defaults[Gradebook :: PROPERTY_END] = $gradebook->get_end();
		$defaults['now'] = date("Y-m-d H:i:00", time());
		parent :: setDefaults($defaults);
	}

	function get_gradebook()
	{
		return $this->gradebook;
	}
}
?>
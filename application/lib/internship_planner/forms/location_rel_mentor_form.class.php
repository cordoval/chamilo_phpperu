<?php
require_once dirname(__FILE__) . '/../location_rel_mentor.class.php';

/**
 * This class describes the form for a InternshipPlannerLocationRelMentor object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipPlannerLocationRelMentorForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $location_rel_mentor;
	private $user;

    function InternshipPlannerLocationRelMentorForm($form_type, $location_rel_mentor, $action, $user)
    {
    	parent :: __construct('location_rel_mentor_settings', 'post', $action);

    	$this->location_rel_mentor = $location_rel_mentor;
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
		$this->addElement('text', InternshipPlannerLocationRelMentor :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(InternshipPlannerLocationRelMentor :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', InternshipPlannerLocationRelMentor :: PROPERTY_MOMENT_ID, Translation :: get('MomentId'));
		$this->addRule(InternshipPlannerLocationRelMentor :: PROPERTY_MOMENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', InternshipPlannerLocationRelMentor :: PROPERTY_LOCATION_ID, Translation :: get('InternshipPlannerLocationId'));
		$this->addRule(InternshipPlannerLocationRelMentor :: PROPERTY_LOCATION_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', InternshipPlannerLocationRelMentor :: PROPERTY_MENTOR_ID, Translation :: get('MentorId'));
		$this->addRule(InternshipPlannerLocationRelMentor :: PROPERTY_MENTOR_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', InternshipPlannerLocationRelMentor :: PROPERTY_STATUS, Translation :: get('Status'));
		$this->addRule(InternshipPlannerLocationRelMentor :: PROPERTY_STATUS, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', InternshipPlannerLocationRelMentor :: PROPERTY_PRIORITY, Translation :: get('Priority'));
		$this->addRule(InternshipPlannerLocationRelMentor :: PROPERTY_PRIORITY, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', InternshipPlannerLocationRelMentor :: PROPERTY_ID);

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

    function update_location_rel_mentor()
    {
    	$location_rel_mentor = $this->location_rel_mentor;
    	$values = $this->exportValues();

    	$location_rel_mentor->set_id($values[InternshipPlannerLocationRelMentor :: PROPERTY_ID]);
    	$location_rel_mentor->set_moment_id($values[InternshipPlannerLocationRelMentor :: PROPERTY_MOMENT_ID]);
    	$location_rel_mentor->set_location_id($values[InternshipPlannerLocationRelMentor :: PROPERTY_LOCATION_ID]);
    	$location_rel_mentor->set_mentor_id($values[InternshipPlannerLocationRelMentor :: PROPERTY_MENTOR_ID]);
    	$location_rel_mentor->set_status($values[InternshipPlannerLocationRelMentor :: PROPERTY_STATUS]);
    	$location_rel_mentor->set_priority($values[InternshipPlannerLocationRelMentor :: PROPERTY_PRIORITY]);

    	return $location_rel_mentor->update();
    }

    function create_location_rel_mentor()
    {
    	$location_rel_mentor = $this->location_rel_mentor;
    	$values = $this->exportValues();

    	$location_rel_mentor->set_id($values[InternshipPlannerLocationRelMentor :: PROPERTY_ID]);
    	$location_rel_mentor->set_moment_id($values[InternshipPlannerLocationRelMentor :: PROPERTY_MOMENT_ID]);
    	$location_rel_mentor->set_location_id($values[InternshipPlannerLocationRelMentor :: PROPERTY_LOCATION_ID]);
    	$location_rel_mentor->set_mentor_id($values[InternshipPlannerLocationRelMentor :: PROPERTY_MENTOR_ID]);
    	$location_rel_mentor->set_status($values[InternshipPlannerLocationRelMentor :: PROPERTY_STATUS]);
    	$location_rel_mentor->set_priority($values[InternshipPlannerLocationRelMentor :: PROPERTY_PRIORITY]);

   		return $location_rel_mentor->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$location_rel_mentor = $this->location_rel_mentor;

    	$defaults[InternshipPlannerLocationRelMentor :: PROPERTY_ID] = $location_rel_mentor->get_id();
    	$defaults[InternshipPlannerLocationRelMentor :: PROPERTY_MOMENT_ID] = $location_rel_mentor->get_moment_id();
    	$defaults[InternshipPlannerLocationRelMentor :: PROPERTY_LOCATION_ID] = $location_rel_mentor->get_location_id();
    	$defaults[InternshipPlannerLocationRelMentor :: PROPERTY_MENTOR_ID] = $location_rel_mentor->get_mentor_id();
    	$defaults[InternshipPlannerLocationRelMentor :: PROPERTY_STATUS] = $location_rel_mentor->get_status();
    	$defaults[InternshipPlannerLocationRelMentor :: PROPERTY_PRIORITY] = $location_rel_mentor->get_priority();

		parent :: setDefaults($defaults);
	}
}
?>
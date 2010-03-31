<?php
require_once dirname(__FILE__) . '/../organisation.class.php';

/**
 * This class describes the form for a Place object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipPlannerOrganisationForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $organisation;
	private $user;

    function InternshipPlannerOrganisationForm($form_type, $organisation, $action, $user)
    {
    	parent :: __construct('organisation_settings', 'post', $action);

    	$this->organisation = $organisation;
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
		
		$this->addElement('text', InternshipPlannerOrganisation :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(InternshipPlannerOrganisation :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', InternshipPlannerOrganisation :: PROPERTY_DESCRIPTION, Translation :: get('Description'));
		$this->addRule(InternshipPlannerOrganisation :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

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

    function update_organisation()
    {
    	$organisation = $this->organisation;
    	$values = $this->exportValues();

    	$organisation->set_name($values[InternshipPlannerOrganisation :: PROPERTY_NAME]);
    	$organisation->set_description($values[InternshipPlannerOrganisation :: PROPERTY_DESCRIPTION]);

    	return $organisation->update();
    }

    function create_organisation()
    {
    	$organisation = $this->organisation;
    	$values = $this->exportValues();

    	$organisation->set_name($values[InternshipPlannerOrganisation :: PROPERTY_NAME]);
    	$organisation->set_description($values[InternshipPlannerOrganisation :: PROPERTY_DESCRIPTION]);
    	    	
   		return $organisation->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
    	$organisation = $this->organisation;
		
    	$defaults[InternshipPlannerOrganisation :: PROPERTY_NAME] = $organisation->get_name();
    	$defaults[InternshipPlannerOrganisation :: PROPERTY_DESCRIPTION] = $organisation->get_description();
    
		parent :: setDefaults($defaults);
	}
}
?>
<?php
require_once dirname(__FILE__) . '/../agreement.class.php';

/**
 * This class describes the form for a Place object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class InternshipPlannerAgreementForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $agreement;
	private $user;

    function InternshipPlannerAgreementForm($form_type, $agreement, $action, $user)
    {
    	parent :: __construct('agreement_settings', 'post', $action);

    	$this->agreement = $agreement;
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
		
		$this->addElement('text', InternshipPlannerAgreement :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(InternshipPlannerAgreement :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', InternshipPlannerAgreement :: PROPERTY_DESCRIPTION, Translation :: get('Description'));
		$this->addRule(InternshipPlannerAgreement :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');

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

    function update_agreement()
    {
    	$agreement = $this->agreement;
    	$values = $this->exportValues();

    	$agreement->set_name($values[InternshipPlannerAgreement :: PROPERTY_NAME]);
    	$agreement->set_description($values[InternshipPlannerAgreement :: PROPERTY_DESCRIPTION]);

    	return $agreement->update();
    }

    function create_agreement()
    {
    	$agreement = $this->agreement;
    	$values = $this->exportValues();

    	$agreement->set_name($values[InternshipPlannerAgreement :: PROPERTY_NAME]);
    	$agreement->set_description($values[InternshipPlannerAgreement :: PROPERTY_DESCRIPTION]);
    	    	
   		return $agreement->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
    	$agreement = $this->agreement;
		
    	$defaults[InternshipPlannerAgreement :: PROPERTY_NAME] = $agreement->get_name();
    	$defaults[InternshipPlannerAgreement :: PROPERTY_DESCRIPTION] = $agreement->get_description();
    
		parent :: setDefaults($defaults);
	}
}
?>
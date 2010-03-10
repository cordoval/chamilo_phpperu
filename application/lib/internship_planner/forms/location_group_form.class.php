<?php
require_once dirname(__FILE__) . '/../location_group.class.php';

/**
 * This class describes the form for a LocationGroup object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class LocationGroupForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $location_group;
	private $user;

    function LocationGroupForm($form_type, $location_group, $action, $user)
    {
    	parent :: __construct('location_group_settings', 'post', $action);

    	$this->location_group = $location_group;
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
		$this->addElement('text', LocationGroup :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(LocationGroup :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', LocationGroup :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(LocationGroup :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', LocationGroup :: PROPERTY_ID);

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

    function update_location_group()
    {
    	$location_group = $this->location_group;
    	$values = $this->exportValues();

    	$location_group->set_id($values[LocationGroup :: PROPERTY_ID]);
    	$location_group->set_name($values[LocationGroup :: PROPERTY_NAME]);

    	return $location_group->update();
    }

    function create_location_group()
    {
    	$location_group = $this->location_group;
    	$values = $this->exportValues();

    	$location_group->set_id($values[LocationGroup :: PROPERTY_ID]);
    	$location_group->set_name($values[LocationGroup :: PROPERTY_NAME]);

   		return $location_group->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$location_group = $this->location_group;

    	$defaults[LocationGroup :: PROPERTY_ID] = $location_group->get_id();
    	$defaults[LocationGroup :: PROPERTY_NAME] = $location_group->get_name();

		parent :: setDefaults($defaults);
	}
}
?>
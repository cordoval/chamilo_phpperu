<?php
require_once dirname(__FILE__) . '/../location_rel_type.class.php';

/**
 * This class describes the form for a LocationRelType object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class LocationRelTypeForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $location_rel_type;
	private $user;

    function LocationRelTypeForm($form_type, $location_rel_type, $action, $user)
    {
    	parent :: __construct('location_rel_type_settings', 'post', $action);

    	$this->location_rel_type = $location_rel_type;
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
		$this->addElement('text', LocationRelType :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(LocationRelType :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', LocationRelType :: PROPERTY_LOCATION_ID, Translation :: get('LocationId'));
		$this->addRule(LocationRelType :: PROPERTY_LOCATION_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', LocationRelType :: PROPERTY_TYPE_ID, Translation :: get('TypeId'));
		$this->addRule(LocationRelType :: PROPERTY_TYPE_ID, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', LocationRelType :: PROPERTY_ID);

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

    function update_location_rel_type()
    {
    	$location_rel_type = $this->location_rel_type;
    	$values = $this->exportValues();

    	$location_rel_type->set_id($values[LocationRelType :: PROPERTY_ID]);
    	$location_rel_type->set_location_id($values[LocationRelType :: PROPERTY_LOCATION_ID]);
    	$location_rel_type->set_type_id($values[LocationRelType :: PROPERTY_TYPE_ID]);

    	return $location_rel_type->update();
    }

    function create_location_rel_type()
    {
    	$location_rel_type = $this->location_rel_type;
    	$values = $this->exportValues();

    	$location_rel_type->set_id($values[LocationRelType :: PROPERTY_ID]);
    	$location_rel_type->set_location_id($values[LocationRelType :: PROPERTY_LOCATION_ID]);
    	$location_rel_type->set_type_id($values[LocationRelType :: PROPERTY_TYPE_ID]);

   		return $location_rel_type->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$location_rel_type = $this->location_rel_type;

    	$defaults[LocationRelType :: PROPERTY_ID] = $location_rel_type->get_id();
    	$defaults[LocationRelType :: PROPERTY_LOCATION_ID] = $location_rel_type->get_location_id();
    	$defaults[LocationRelType :: PROPERTY_TYPE_ID] = $location_rel_type->get_type_id();

		parent :: setDefaults($defaults);
	}
}
?>
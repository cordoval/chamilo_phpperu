<?php
require_once dirname(__FILE__) . '/../place.class.php';

/**
 * This class describes the form for a Place object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class PlaceForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $place;
	private $user;

    function PlaceForm($form_type, $place, $action, $user)
    {
    	parent :: __construct('place_settings', 'post', $action);

    	$this->place = $place;
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
		$this->addElement('text', Place :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(Place :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Place :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(Place :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Place :: PROPERTY_PARENT_ID, Translation :: get('ParentId'));
		$this->addRule(Place :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Place :: PROPERTY_LEFT_VALUE, Translation :: get('LeftValue'));
		$this->addRule(Place :: PROPERTY_LEFT_VALUE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Place :: PROPERTY_RIGHT_VALUE, Translation :: get('RightValue'));
		$this->addRule(Place :: PROPERTY_RIGHT_VALUE, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', Place :: PROPERTY_ID);

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

    function update_place()
    {
    	$place = $this->place;
    	$values = $this->exportValues();

    	$place->set_id($values[Place :: PROPERTY_ID]);
    	$place->set_name($values[Place :: PROPERTY_NAME]);
    	$place->set_parent_id($values[Place :: PROPERTY_PARENT_ID]);
    	$place->set_left_value($values[Place :: PROPERTY_LEFT_VALUE]);
    	$place->set_right_value($values[Place :: PROPERTY_RIGHT_VALUE]);

    	return $place->update();
    }

    function create_place()
    {
    	$place = $this->place;
    	$values = $this->exportValues();

    	$place->set_id($values[Place :: PROPERTY_ID]);
    	$place->set_name($values[Place :: PROPERTY_NAME]);
    	$place->set_parent_id($values[Place :: PROPERTY_PARENT_ID]);
    	$place->set_left_value($values[Place :: PROPERTY_LEFT_VALUE]);
    	$place->set_right_value($values[Place :: PROPERTY_RIGHT_VALUE]);

   		return $place->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$place = $this->place;

    	$defaults[Place :: PROPERTY_ID] = $place->get_id();
    	$defaults[Place :: PROPERTY_NAME] = $place->get_name();
    	$defaults[Place :: PROPERTY_PARENT_ID] = $place->get_parent_id();
    	$defaults[Place :: PROPERTY_LEFT_VALUE] = $place->get_left_value();
    	$defaults[Place :: PROPERTY_RIGHT_VALUE] = $place->get_right_value();

		parent :: setDefaults($defaults);
	}
}
?>
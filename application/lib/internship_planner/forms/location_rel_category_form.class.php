<?php
require_once dirname(__FILE__) . '/../location_rel_category.class.php';

/**
 * This class describes the form for a LocationRelCategory object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class LocationRelCategoryForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $location_rel_category;
	private $user;

    function LocationRelCategoryForm($form_type, $location_rel_category, $action, $user)
    {
    	parent :: __construct('location_rel_category_settings', 'post', $action);

    	$this->location_rel_category = $location_rel_category;
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
		$this->addElement('text', LocationRelCategory :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(LocationRelCategory :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', LocationRelCategory :: PROPERTY_LOCATION_ID, Translation :: get('LocationId'));
		$this->addRule(LocationRelCategory :: PROPERTY_LOCATION_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', LocationRelCategory :: PROPERTY_CATEGORY_ID, Translation :: get('CategoryId'));
		$this->addRule(LocationRelCategory :: PROPERTY_CATEGORY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', LocationRelCategory :: PROPERTY_ID);

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

    function update_location_rel_category()
    {
    	$location_rel_category = $this->location_rel_category;
    	$values = $this->exportValues();

    	$location_rel_category->set_id($values[LocationRelCategory :: PROPERTY_ID]);
    	$location_rel_category->set_location_id($values[LocationRelCategory :: PROPERTY_LOCATION_ID]);
    	$location_rel_category->set_category_id($values[LocationRelCategory :: PROPERTY_CATEGORY_ID]);

    	return $location_rel_category->update();
    }

    function create_location_rel_category()
    {
    	$location_rel_category = $this->location_rel_category;
    	$values = $this->exportValues();

    	$location_rel_category->set_id($values[LocationRelCategory :: PROPERTY_ID]);
    	$location_rel_category->set_location_id($values[LocationRelCategory :: PROPERTY_LOCATION_ID]);
    	$location_rel_category->set_category_id($values[LocationRelCategory :: PROPERTY_CATEGORY_ID]);

   		return $location_rel_category->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$location_rel_category = $this->location_rel_category;

    	$defaults[LocationRelCategory :: PROPERTY_ID] = $location_rel_category->get_id();
    	$defaults[LocationRelCategory :: PROPERTY_LOCATION_ID] = $location_rel_category->get_location_id();
    	$defaults[LocationRelCategory :: PROPERTY_CATEGORY_ID] = $location_rel_category->get_category_id();

		parent :: setDefaults($defaults);
	}
}
?>
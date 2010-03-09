<?php
require_once dirname(__FILE__) . '/../category.class.php';

/**
 * This class describes the form for a Category object.
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 **/
class CategoryForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $category;
	private $user;

    function CategoryForm($form_type, $category, $action, $user)
    {
    	parent :: __construct('category_settings', 'post', $action);

    	$this->category = $category;
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
		$this->addElement('text', Category :: PROPERTY_ID, Translation :: get('Id'));
		$this->addRule(Category :: PROPERTY_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Category :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(Category :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Category :: PROPERTY_DESCRIPTION, Translation :: get('Description'));
		$this->addRule(Category :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Category :: PROPERTY_PARENT_ID, Translation :: get('ParentId'));
		$this->addRule(Category :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Category :: PROPERTY_LEFT_VALUE, Translation :: get('LeftValue'));
		$this->addRule(Category :: PROPERTY_LEFT_VALUE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', Category :: PROPERTY_RIGHT_VALUE, Translation :: get('RightValue'));
		$this->addRule(Category :: PROPERTY_RIGHT_VALUE, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', Category :: PROPERTY_ID);

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

    function update_category()
    {
    	$category = $this->category;
    	$values = $this->exportValues();

    	$category->set_id($values[Category :: PROPERTY_ID]);
    	$category->set_name($values[Category :: PROPERTY_NAME]);
    	$category->set_description($values[Category :: PROPERTY_DESCRIPTION]);
    	$category->set_parent_id($values[Category :: PROPERTY_PARENT_ID]);
    	$category->set_left_value($values[Category :: PROPERTY_LEFT_VALUE]);
    	$category->set_right_value($values[Category :: PROPERTY_RIGHT_VALUE]);

    	return $category->update();
    }

    function create_category()
    {
    	$category = $this->category;
    	$values = $this->exportValues();

    	$category->set_id($values[Category :: PROPERTY_ID]);
    	$category->set_name($values[Category :: PROPERTY_NAME]);
    	$category->set_description($values[Category :: PROPERTY_DESCRIPTION]);
    	$category->set_parent_id($values[Category :: PROPERTY_PARENT_ID]);
    	$category->set_left_value($values[Category :: PROPERTY_LEFT_VALUE]);
    	$category->set_right_value($values[Category :: PROPERTY_RIGHT_VALUE]);

   		return $category->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$category = $this->category;

    	$defaults[Category :: PROPERTY_ID] = $category->get_id();
    	$defaults[Category :: PROPERTY_NAME] = $category->get_name();
    	$defaults[Category :: PROPERTY_DESCRIPTION] = $category->get_description();
    	$defaults[Category :: PROPERTY_PARENT_ID] = $category->get_parent_id();
    	$defaults[Category :: PROPERTY_LEFT_VALUE] = $category->get_left_value();
    	$defaults[Category :: PROPERTY_RIGHT_VALUE] = $category->get_right_value();

		parent :: setDefaults($defaults);
	}
}
?>
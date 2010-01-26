<?php
require_once dirname(__FILE__) . '/../language_pack.class.php';

/**
 * This class describes the form for a LanguagePack object.
 * @author Sven Vanpoucke
 * @author 
 **/
class LanguagePackForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $language_pack;
	private $user;

    function LanguagePackForm($form_type, $language_pack, $action, $user)
    {
    	parent :: __construct('language_pack_settings', 'post', $action);

    	$this->language_pack = $language_pack;
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
		$this->addElement('text', LanguagePack :: PROPERTY_NAME, Translation :: get('Name'));
		$this->addRule(LanguagePack :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$types = array();
		$types[LanguagePack :: TYPE_APPLICATION] = 'Application';
		$types[LanguagePack :: TYPE_CORE] = 'Core';
    	$this->addElement('select',LanguagePack :: PROPERTY_TYPE, Translation :: get('Type'), $types);
    	$this->addRule(LanguagePack :: PROPERTY_TYPE, Translation :: get('ThisFieldIsRequired'), 'required');
    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', LanguagePack :: PROPERTY_ID);

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

    function update_language_pack()
    {
    	$language_pack = $this->language_pack;
    	$values = $this->exportValues();

    	$language_pack->set_name($values[LanguagePack :: PROPERTY_NAME]);
    	$language_pack->set_type($values[LanguagePack :: PROPERTY_TYPE]);

    	return $language_pack->update();
    }

    function create_language_pack()
    {
    	$language_pack = $this->language_pack;
    	$values = $this->exportValues();

    	$language_pack->set_name($values[LanguagePack :: PROPERTY_NAME]);
    	$language_pack->set_type($values[LanguagePack :: PROPERTY_TYPE]);

   		return $language_pack->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$language_pack = $this->language_pack;

    	$defaults[LanguagePack :: PROPERTY_NAME] = $language_pack->get_name();
    	$defaults[LanguagePack :: PROPERTY_TYPE] = $language_pack->get_type();

		parent :: setDefaults($defaults);
	}
}
?>
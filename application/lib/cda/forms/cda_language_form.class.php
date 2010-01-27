<?php
require_once dirname(__FILE__) . '/../cda_language.class.php';

/**
 * This class describes the form for a CdaLanguage object.
 * @author Sven Vanpoucke
 * @author 
 **/
class CdaLanguageForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $cda_language;
	private $user;

    function CdaLanguageForm($form_type, $cda_language, $action, $user)
    {
    	parent :: __construct('cda_language_settings', 'post', $action);

    	$this->cda_language = $cda_language;
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
		$this->addElement('text', CdaLanguage :: PROPERTY_ORIGINAL_NAME, Translation :: get('OriginalName'));
		$this->addRule(CdaLanguage :: PROPERTY_ORIGINAL_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', CdaLanguage :: PROPERTY_ENGLISH_NAME, Translation :: get('EnglishName'));
		$this->addRule(CdaLanguage :: PROPERTY_ENGLISH_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', CdaLanguage :: PROPERTY_ISOCODE, Translation :: get('Isocode'));
		$this->addRule(CdaLanguage :: PROPERTY_ISOCODE, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', CdaLanguage :: PROPERTY_ID);

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

    function update_cda_language()
    {
    	$cda_language = $this->cda_language;
    	$values = $this->exportValues();
    	
    	$cda_language->set_original_name($values[CdaLanguage :: PROPERTY_ORIGINAL_NAME]);
    	$cda_language->set_english_name($values[CdaLanguage :: PROPERTY_ENGLISH_NAME]);
    	$cda_language->set_isocode($values[CdaLanguage :: PROPERTY_ISOCODE]);

    	return $cda_language->update();
    }

    function create_cda_language()
    {
    	$cda_language = $this->cda_language;
    	$values = $this->exportValues();
    	
    	$cda_language->set_original_name($values[CdaLanguage :: PROPERTY_ORIGINAL_NAME]);
    	$cda_language->set_english_name($values[CdaLanguage :: PROPERTY_ENGLISH_NAME]);
    	$cda_language->set_isocode($values[CdaLanguage :: PROPERTY_ISOCODE]);

   		return $cda_language->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$cda_language = $this->cda_language;

    	$defaults[CdaLanguage :: PROPERTY_ORIGINAL_NAME] = $cda_language->get_original_name();
    	$defaults[CdaLanguage :: PROPERTY_ENGLISH_NAME] = $cda_language->get_english_name();
    	$defaults[CdaLanguage :: PROPERTY_ISOCODE] = $cda_language->get_isocode();

		parent :: setDefaults($defaults);
	}
}
?>
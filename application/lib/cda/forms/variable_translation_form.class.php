<?php
require_once dirname(__FILE__) . '/../variable_translation.class.php';

/**
 * This class describes the form for a VariableTranslation object.
 * @author Sven Vanpoucke
 * @author 
 **/
class VariableTranslationForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $variable_translation;
	private $user;

    function VariableTranslationForm($form_type, $variable_translation, $action, $user)
    {
    	parent :: __construct('variable_translation_settings', 'post', $action);

    	$this->variable_translation = $variable_translation;
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
		$this->addElement('text', VariableTranslation :: PROPERTY_LANGUAGE_ID, Translation :: get('LanguageId'));
		$this->addRule(VariableTranslation :: PROPERTY_LANGUAGE_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', VariableTranslation :: PROPERTY_VARIABLE_ID, Translation :: get('VariableId'));
		$this->addRule(VariableTranslation :: PROPERTY_VARIABLE_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', VariableTranslation :: PROPERTY_TRANSLATION, Translation :: get('Translation'));
		$this->addRule(VariableTranslation :: PROPERTY_TRANSLATION, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', VariableTranslation :: PROPERTY_DATE, Translation :: get('Date'));
		$this->addRule(VariableTranslation :: PROPERTY_DATE, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', VariableTranslation :: PROPERTY_USER_ID, Translation :: get('UserId'));
		$this->addRule(VariableTranslation :: PROPERTY_USER_ID, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', VariableTranslation :: PROPERTY_RATING, Translation :: get('Rating'));
		$this->addRule(VariableTranslation :: PROPERTY_RATING, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', VariableTranslation :: PROPERTY_RATED, Translation :: get('Rated'));
		$this->addRule(VariableTranslation :: PROPERTY_RATED, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('text', VariableTranslation :: PROPERTY_STATUS, Translation :: get('Status'));
		$this->addRule(VariableTranslation :: PROPERTY_STATUS, Translation :: get('ThisFieldIsRequired'), 'required');

    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', VariableTranslation :: PROPERTY_ID);

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

    function update_variable_translation()
    {
    	$variable_translation = $this->variable_translation;
    	$values = $this->exportValues();

    	$variable_translation->set_language_id($values[VariableTranslation :: PROPERTY_LANGUAGE_ID]);
    	$variable_translation->set_variable_id($values[VariableTranslation :: PROPERTY_VARIABLE_ID]);
    	$variable_translation->set_translation($values[VariableTranslation :: PROPERTY_TRANSLATION]);
    	$variable_translation->set_date($values[VariableTranslation :: PROPERTY_DATE]);
    	$variable_translation->set_user_id($values[VariableTranslation :: PROPERTY_USER_ID]);
    	$variable_translation->set_rating($values[VariableTranslation :: PROPERTY_RATING]);
    	$variable_translation->set_rated($values[VariableTranslation :: PROPERTY_RATED]);
    	$variable_translation->set_status($values[VariableTranslation :: PROPERTY_STATUS]);

    	return $variable_translation->update();
    }

    function create_variable_translation()
    {
    	$variable_translation = $this->variable_translation;
    	$values = $this->exportValues();

    	$variable_translation->set_language_id($values[VariableTranslation :: PROPERTY_LANGUAGE_ID]);
    	$variable_translation->set_variable_id($values[VariableTranslation :: PROPERTY_VARIABLE_ID]);
    	$variable_translation->set_translation($values[VariableTranslation :: PROPERTY_TRANSLATION]);
    	$variable_translation->set_date($values[VariableTranslation :: PROPERTY_DATE]);
    	$variable_translation->set_user_id($values[VariableTranslation :: PROPERTY_USER_ID]);
    	$variable_translation->set_rating($values[VariableTranslation :: PROPERTY_RATING]);
    	$variable_translation->set_rated($values[VariableTranslation :: PROPERTY_RATED]);
    	$variable_translation->set_status($values[VariableTranslation :: PROPERTY_STATUS]);

   		return $variable_translation->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$variable_translation = $this->variable_translation;

    	$defaults[VariableTranslation :: PROPERTY_LANGUAGE_ID] = $variable_translation->get_language_id();
    	$defaults[VariableTranslation :: PROPERTY_VARIABLE_ID] = $variable_translation->get_variable_id();
    	$defaults[VariableTranslation :: PROPERTY_TRANSLATION] = $variable_translation->get_translation();
    	$defaults[VariableTranslation :: PROPERTY_DATE] = $variable_translation->get_date();
    	$defaults[VariableTranslation :: PROPERTY_USER_ID] = $variable_translation->get_user_id();
    	$defaults[VariableTranslation :: PROPERTY_RATING] = $variable_translation->get_rating();
    	$defaults[VariableTranslation :: PROPERTY_RATED] = $variable_translation->get_rated();
    	$defaults[VariableTranslation :: PROPERTY_STATUS] = $variable_translation->get_status();

		parent :: setDefaults($defaults);
	}
}
?>
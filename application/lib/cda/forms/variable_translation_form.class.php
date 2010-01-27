<?php
require_once dirname(__FILE__) . '/../variable_translation.class.php';

/**
 * This class describes the form for a VariableTranslation object.
 * @author Sven Vanpoucke
 * @author
 **/
class VariableTranslationForm extends FormValidator
{
	private $variable_translation;
	private $variable;
	private $user;

    function VariableTranslationForm($variable_translation, $variable, $action, $user)
    {
    	parent :: __construct('variable_translation_settings', 'post', $action);

    	$this->variable_translation = $variable_translation;
    	$this->variable = $variable;

    	$this->user = $user;

		$this->build_basic_form();

		$this->setDefaults();
    }

    function build_basic_form()
    {
		$this->addElement('category', Translation :: get('Information'));

		$html = array();
		$html[] = '<div class="row">';
		$html[] = '<div class="label">' . Translation :: get('Variable') . '</div> ';
		$html[] = '<div class="formw"><div class="element">' . $this->variable->get_variable() . '</div></div>';

		$english_variable = CdaDataManager :: get_instance()->retrieve_english_translation($this->variable->get_id());

		if ($english_variable && $this->variable_translation->get_language_id() != $english_variable->get_language_id())
		{
		    $translation = $english_variable->get_translation();

		    if (isset($translation))
		    {
        		$html[] = '<div class="label">' . Translation :: get('EnglishTranslation') . '</div> ';
        		$html[] = '<div class="formw"><div class="element">';
        		$html[] = $translation . '</div></div>';
		    }
		}

		$html[] = '</div>';
		$html[] = '<br /><br />';

		$this->addElement('html', implode("\n", $html));

    	$this->addElement('category');

    	$this->addElement('category', Translation :: get('Translation'));

    	$this->addElement('textarea', VariableTranslation :: PROPERTY_TRANSLATION, Translation :: get('Translation'), array('style' => 'width: 500px; height: 250px;'));
		$this->addRule(VariableTranslation :: PROPERTY_TRANSLATION, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('category');

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Translate'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_variable_translation()
    {
    	$variable_translation = $this->variable_translation;
    	$values = $this->exportValues();

    	$variable_translation->set_translation($values[VariableTranslation :: PROPERTY_TRANSLATION]);
		$variable_translation->set_date(Utilities :: to_db_date(time()));

    	return $variable_translation->update();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$variable_translation = $this->variable_translation;

    	$defaults[VariableTranslation :: PROPERTY_TRANSLATION] = $variable_translation->get_translation();

		parent :: setDefaults($defaults);
	}
}
?>
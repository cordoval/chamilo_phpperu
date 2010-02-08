<?php
require_once dirname(__FILE__) . '/../variable_translation.class.php';

/**
 * This class describes the form for a VariableTranslation object.
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 **/
class VariableTranslationForm extends FormValidator
{
	private $variable_translation;
	private $variable;
	private $user;
	
	const SUBMIT_NEXT = 'next';
	const SUBMIT_SAVE = 'save';
	const SUBMIT_NEXT_NO_SAVE = 'next_no_save';

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
		
    	$this->addElement('category', Translation :: get('Reference'));
		$this->addElement('static', null, Translation :: get('Variable'), $this->variable->get_variable());
		
    	$language_id = $this->variable_translation->get_language_id();		
		$source_id = LocalSetting :: get('source_language', CdaManager :: APPLICATION_NAME);
		$english_id = CdaDataManager :: get_instance()->retrieve_cda_language_english()->get_id();
		
		if ($english_id != $language_id)
		{
			$english = CdaDataManager :: get_instance()->retrieve_english_translation($this->variable->get_id());
	        $english_translation = ($english && $english->get_translation() != ' ') ? $english->get_translation() : Translation :: get('NoTranslation');
	        $this->addElement('static', null, Translation :: get('EnglishTranslation'), $english_translation);
		}
		
		if($source_id != $english_id)
		{
			$source = CdaDataManager :: get_instance()->retrieve_variable_translation_by_parameters($source_id, $this->variable->get_id());
			$source_translation = ($source && $source->get_translation() != ' ') ? $source->get_translation() : Translation :: get('NoTranslation');
			$this->addElement('static', null, Translation :: get('SourceTranslation'), $source_translation);
		}
		
    	$this->addElement('category');
    	
    	$this->addElement('category', Translation :: get('Translation'));
    	$this->addElement('textarea', VariableTranslation :: PROPERTY_TRANSLATION, Translation :: get('Translation'), array('style' => 'width: 500px; height: 250px;', 'id' => 'translation'));
		$this->addRule(VariableTranslation :: PROPERTY_TRANSLATION, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addElement('category');
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('TranslateNextVariable'), array('class' => 'normal continue'), self :: SUBMIT_NEXT);
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('GoToNextVariableWithoutSave'), array('class' => 'normal next'), self :: SUBMIT_NEXT_NO_SAVE);
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Translate'), array('class' => 'positive save'), self :: SUBMIT_SAVE);
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
		
		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		
		$this->add_other_translations($english_id);
    }

    function update_variable_translation()
    {
    	$variable_translation = $this->variable_translation;
    	$values = $this->exportValues();

    	$variable_translation->set_translation($values[VariableTranslation :: PROPERTY_TRANSLATION]);
		$variable_translation->set_date(Utilities :: to_db_date(time()));
		$variable_translation->set_user_id($this->user->get_id());
    	
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
	
	function get_submit_type()
	{
		$button_values = $this->exportValue('buttons');
		return $button_values['submit'];
	}
	
	function add_other_translations($english_id)
	{
		$this->addElement('html', '<a href="#" id="show" style="display: none;">' . Translation :: get('ShowOtherTranslations') . '</a>');
		$this->addElement('html', '<a href="#" id="hide" style="display: none;">' . Translation :: get('HideOtherTranslations') . '</a>');
		$this->addElement('html', '<div id="othertranslations">');
		$this->addElement('category', Translation :: get('OtherTranslations'));
		
		$conditions = array();
		$conditions[] = new NotCondition(new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $english_id));
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $this->variable->get_id());
		
		$dm = CdaDataManager :: get_instance();
		$translations = $dm->retrieve_variable_translations(new AndCondition($conditions));
		while($translation = $translations->next_result())
		{
			$trans = $translation->get_translation();
			$language = $dm->retrieve_cda_language($translation->get_language_id());
			$html[] = '<b>' . $language->get_original_name() . '</b>: ' . ($trans ? $trans : Translation :: get('NoTranslation'));
		}
		
		$this->addElement('html', '<div style="margin-left: 20px;">' . implode("<br />", $html) . '<br /><br /></div>');
		
		$this->addElement('category');
		$this->addElement('html', '</div>');
	}
	
	function validate()
	{
		$button_values = $this->exportValue('buttons');
		if($button_values['submit'] == self :: SUBMIT_NEXT_NO_SAVE)
			return true;
			
		return parent :: validate();
	}
}
?>
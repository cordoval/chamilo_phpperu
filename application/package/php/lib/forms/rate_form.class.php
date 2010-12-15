<?php

namespace application\package;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Utilities;
/**
 * @package 
 * 
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class RateForm extends FormValidator 
{

	private $translation;
	private $variable;

	/**
	 * Creates a new RateForm
	 */
    function __construct($translation, $variable, $action) 
    {
    	parent :: __construct('rate_form', 'post', $action);

		$this->translation = $translation;
		$this->variable = $variable;
		$this->build_form();
		$this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    function build_form()
    {	
    	$this->addElement('category', Translation :: get('Reference'));
		$this->addElement('static', null, Translation :: get('Variable'), $this->variable->get_variable());
		$english = PackageDataManager :: get_instance()->retrieve_english_translation($this->variable->get_id());
        $english_translation = ($english && $english->get_translation() != ' ') ? $english->get_translation() : Translation :: get('NoTranslation');
        $this->addElement('static', null, Translation :: get('EnglishTranslation'), $english_translation);
        $this->addElement('category');
		
        $this->addElement('category', Translation :: get('Translation'));
    	$this->addElement('static', null, Translation :: get('TargetLanguage'), $this->translation->get_translation());
    	$this->addElement('category');
    	
    	$this->addElement('category', Translation :: get('Rate'));
    	
    	// Rating
    	$rating = array();
		for($i = 1; $i <= 10; $i++)
		{
			$rating[$i] = $i;	
		}
		
    	$this->addElement('select',VariableTranslation :: PROPERTY_RATING, Translation :: get('Rating'), $rating);
    	$this->addRule(VariableTranslation :: PROPERTY_RATING, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
		
		$this->addElement('category');  

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Rate'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

	function rate()
	{
		$values = $this->exportValues();
		$translation = $this->translation;
		$translation->set_rating($translation->get_rating() + $values[VariableTranslation :: PROPERTY_RATING]);
		$translation->set_rated($translation->get_rated() + 1);
		return $translation->update();
	}
	
	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		if($this->translation->get_rated() != 0)
		{
			$defaults[VariableTranslation :: PROPERTY_RATING] = $this->translation->get_rating();	
		}
		else
		{
			$defaults[VariableTranslation :: PROPERTY_RATING] = 5;
		}
		parent :: setDefaults($defaults);
	}
}
?>
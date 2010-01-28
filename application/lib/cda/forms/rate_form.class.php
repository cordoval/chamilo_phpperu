<?php
/**
 * @package 
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';

class RateForm extends FormValidator 
{

	private $translation;
	private $variable;
	private $user_id;

	/**
	 * Creates a new RateForm
	 */
    function RateForm($translation, $variable, $action, $user_id) 
    {
    	parent :: __construct('rate_form', 'post', $action);

		$this->translation = $translation;
		$this->variable = $variable;
		$this->user_id = $user_id;
		$this->build_form();
		$this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    function build_form()
    {	
    	$this->addElement('category', Translation :: get('Information'));
		
		$html = array();
		$html[] = '<div class="row">';
		$html[] = '<div class="label">' . Translation :: get('Variable') . '</div> ';
		$html[] = '<div class="formw"><div class="element">' . $this->variable->get_variable() . '</div></div>';
		$html[] = '<div class="label">' . Translation :: get('EnglishTranslation') . '</div> '; 
		$html[] = '<div class="formw"><div class="element">';
		
		$english = CdaDataManager :: get_instance()->retrieve_english_translation($this->variable->get_id());
        $english_translation = ($english && $english->get_translation() != ' ') ? $english->get_translation() : Translation :: get('NoTranslation');
		$html[] = $english_translation . '</div></div>';
		
		$html[] = '</div><br /><br />';
		
		$this->addElement('html', implode("\n", $html));
		
    	$this->addElement('category');
    	
    	$this->addElement('category', Translation :: get('Rate'));
    	
    	// Rating
    	$rating = array();
		for($i = 1; $i <= 10; $i++)
		{
			$rating[$i] = $i;	
		}
		
    	$this->addElement('select',VariableTranslation :: PROPERTY_RATING, Translation :: get('Rating'), $rating);
    	$this->addRule(VariableTranslation :: PROPERTY_RATING, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('category');  

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Rate'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

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
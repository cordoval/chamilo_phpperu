<?php
/**
 * $Id: variable_translation_browser_filter_form.class.php 196 2009-11-13 12:19:18Z chellee $
 */

class VariableTranslationSearchForm extends FormValidator
{
    const SEARCH_STRING = 'search_string';

    private $manager;
    
    /**
     * Creates a new search form
     * @param RepositoryManager $manager The repository manager in which this
     * search form will be displayed
     * @param string $url The location to which the search request should be
     * posted.
     */
    function __construct($manager, $url)
    {
        parent :: __construct('variable_translation_search_form', 'post', $url);
        
        $this->manager = $manager;
        $this->build_form();
        $this->setDefaults();
    }

    /**
     * Build the simple search form.
     */
    private function build_form()
    {
        $this->addElement('text', Variable :: PROPERTY_VARIABLE, Translation :: get('Variable'));
        $this->addRule(Variable :: PROPERTY_VARIABLE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('select', VariableTranslation :: PROPERTY_LANGUAGE_ID, Translation :: get('Language'), $this->get_languages());
        $this->addRule(VariableTranslation :: PROPERTY_LANGUAGE_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $branches = LanguagePack :: get_branch_options();
        $branches[0] = Translation :: get('Both');
        ksort($branches);
        
        $this->addElement('select', LanguagePack :: PROPERTY_BRANCH, Translation :: get('Branch'), $branches);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Search'), array('class' => 'positive search'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    private function get_languages()
    {
    	$cda_languages = $this->manager->retrieve_cda_languages(null, null, null, new ObjectTableOrder(CdaLanguage :: PROPERTY_ENGLISH_NAME));
    	
    	$languages = array();
    	
    	while($language = $cda_languages->next_result())
    	{
    		$languages[$language->get_id()] = $language->get_english_name();
    	}
    	
    	return $languages;
    }

    function get_search_conditions()
    {
        if (! $this->validate() && ! $this->get_parameters_are_set())
        {
            return null;
        }

        $search_string = Session :: retrieve(self :: SEARCH_STRING);

        $form_validates = $this->validate();

        if ($form_validates)
        {
            $values = $this->exportValues(); 
            unset($values['submit']);
            $search_string = serialize($values);
            Session :: register(self :: SEARCH_STRING, $search_string);
        }

        $search_parameters = unserialize($search_string);
        
        if(is_array($search_parameters) && count($search_parameters) > 0)
        {
        	$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $search_parameters[VariableTranslation :: PROPERTY_LANGUAGE_ID]);
        	
        	$branch = $search_parameters[LanguagePack :: PROPERTY_BRANCH];
        	
        	if($branch)
        	{
        		$subcondition1 = new EqualityCondition(LanguagePack :: PROPERTY_BRANCH, $branch);
        		$subconditions[] = new SubselectCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, LanguagePack :: PROPERTY_ID, 
        						LanguagePack :: get_table_name(), $subcondition1);
        		$subconditions[] = new PatternMatchCondition(Variable :: PROPERTY_VARIABLE, '*' . $search_parameters[Variable :: PROPERTY_VARIABLE] . '*');
        		$subcondition = new AndCondition($subconditions);
        		$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 
        						Variable :: get_table_name(), $subcondition);
        	}
        	else
        	{
        		$subcondition = new PatternMatchCondition(Variable :: PROPERTY_VARIABLE, '*' . $search_parameters[Variable :: PROPERTY_VARIABLE] . '*');
        		$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 
        						Variable :: get_table_name(), $subcondition);
        	}
        	
        	return new AndCondition($conditions);
        }
        
     	return null;
    }

    function get_parameters_are_set()
    {
        $search_string = Session :: retrieve(self :: SEARCH_STRING);
		
        return isset($search_string);
    }

    function setDefaults($defaults = array ())
    {
        $search_string = Session :: retrieve(self :: SEARCH_STRING);

        if ($search_string)
        {
            $defaults = unserialize($search_string);
        }

        parent :: setDefaults($defaults);
    }
}
?>
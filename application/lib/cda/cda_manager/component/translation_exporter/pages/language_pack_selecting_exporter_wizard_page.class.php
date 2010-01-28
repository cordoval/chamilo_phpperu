<?php
/**
 * $Id: language_pack_selecting_exporter_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 */

class LanguagePackSelectingExporterWizardPage extends ExporterWizardPage
{

    function get_title()
    {
        return Translation :: get('');
    }

    function get_info()
    {
        return '';
    }

    function buildForm()
    {
    	$this->_formBuilt = true;
    	
    	$values = $this->get_parent()->exportValues();
    	
    	$branch = $values[LanguagePack :: PROPERTY_BRANCH];
    	if($branch)
    	{
    		$condition = new EqualityCondition(LanguagePack :: PROPERTY_BRANCH, $branch);
    	}
    	
    	$language_packs = $this->get_parent()->get_parent()->retrieve_language_packs($condition);
    	while($language_pack = $language_packs->next_result())
    	{
    		$this->addElement('checkbox', 'language_packs[' . $language_pack->get_id() . ']', '', $language_pack->get_name());
    	}
    	
    	$buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
    }

}
?>
<?php
/**
 * $Id: language_pack_selecting_exporter_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 */

class LanguagePackSelectingExporterWizardPage extends ExporterWizardPage
{

    function get_title()
    {
        return Translation :: get('SelectLanguagePackTitle');
    }

    function get_info()
    {
        return Translation :: get('SelectLanguagePackDescription');
    }

    function buildForm()
    {
    	$this->_formBuilt = true;
    	
    	$this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/cda_export.js'));
    	
    	$values = $this->get_parent()->exportValues();
    	
    	$branch = $values[LanguagePack :: PROPERTY_BRANCH];
    	if($branch)
    	{
    		$condition = new EqualityCondition(LanguagePack :: PROPERTY_BRANCH, $branch);
    	}
    	
    	$language_packs = $this->get_parent()->get_parent()->retrieve_language_packs($condition, null, null, 
    			array(new ObjectTableOrder(LanguagePack :: PROPERTY_TYPE), new ObjectTableOrder(LanguagePack :: PROPERTY_NAME)));
    			
    	$previous_type = 0;
    			
    	while($language_pack = $language_packs->next_result())
    	{
    		if($language_pack->get_type() != $previous_type)
    		{
    			$this->addElement('checkbox', $language_pack->get_type_string(), '', $language_pack->get_type_name(), 'class="lptype" style=\'margin-top: 20px;\'');
    			$previous_type = $language_pack->get_type();
    		}
    		
    		$this->addElement('checkbox', 'language_packs[' . $language_pack->get_id() . ']', '', $language_pack->get_name(), 
    						  array('class' => 'lp_' . $language_pack->get_type_string(), 'style' => 'margin-left: 20px;'));
    	}
    	
    	$buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
    }

}
?>
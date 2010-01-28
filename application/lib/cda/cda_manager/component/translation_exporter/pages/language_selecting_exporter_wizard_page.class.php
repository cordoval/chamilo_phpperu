<?php
/**
 * $Id: language_selecting_exporter_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 */

class LanguageSelectingExporterWizardPage extends ExporterWizardPage
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
		
    	$languages = $this->get_parent()->get_parent()->retrieve_cda_languages();
    	while($language = $languages->next_result())
    	{
    		$this->addElement('checkbox', 'language[' . $language->get_id() . ']', '', $language->get_english_name());
    	}
    	
    	$buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
    	
    }

}
?>
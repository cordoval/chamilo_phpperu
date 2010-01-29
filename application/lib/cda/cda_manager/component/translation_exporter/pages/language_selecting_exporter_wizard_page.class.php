<?php
/**
 * $Id: language_selecting_exporter_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 */

class LanguageSelectingExporterWizardPage extends ExporterWizardPage
{

    function get_title()
    {
        return Translation :: get('SelectLanguageDescription');
    }

	function get_info()
    {
        return Translation :: get('SelectLanguageDescription');
    }

    function buildForm()
    {
    	$this->_formBuilt = true;
		
    	$this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/cda_export.js'));
    	
    	$html[] = '<div style="margin-left: 20%">';
    	$html[] = '<a href="#" id="selectall_languages">' . Translation :: get('SelectAll') . '</a> - ';
    	$html[] = '<a href="#" id="unselectall_languages">' . Translation :: get('UnSelectAll') . '</a>';
    	$html[] = '</div><br />';
    	
    	$this->addElement('html', implode("\n", $html));
     	
    	$languages = $this->get_parent()->get_parent()->retrieve_cda_languages(null, null, null, new ObjectTableOrder(CdaLanguage :: PROPERTY_ENGLISH_NAME));
    	while($language = $languages->next_result())
    	{
    		$this->addElement('checkbox', 'language[' . $language->get_id() . ']', '', $language->get_english_name(), array('class' => 'language'));
    	}
    	
    	$this->addElement('html', '<br />' . implode("\n", $html));
    	
    	$buttons = array();
    	$buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Previous'), array('class' => 'normal previous'));
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
    	
    }

}
?>
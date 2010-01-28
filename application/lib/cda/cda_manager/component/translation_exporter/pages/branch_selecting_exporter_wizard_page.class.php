<?php
/**
 * $Id: branch_selecting_exporter_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 */

class BranchSelectingExporterWizardPage extends ExporterWizardPage
{

    function get_title()
    {
        return Translation :: get('SelectBranchTitle');
    }

    function get_info()
    {
        return Translation :: get('SelectBranchDescription');
    }

    function buildForm()
    {
    	$this->_formBuilt = true;
		
    	$branches = array();
		$branches[LanguagePack :: BRANCH_CLASSIC] = Translation :: get('ChamiloClassic');
		$branches[LanguagePack :: BRANCH_LCMS] = Translation :: get('ChamiloLCMS');
    	$this->addElement('select',LanguagePack :: PROPERTY_BRANCH, Translation :: get('Branch'), $branches);
    	
    	$buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
    }

}
?>
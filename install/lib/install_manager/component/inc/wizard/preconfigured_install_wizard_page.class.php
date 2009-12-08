<?php
/**
 * $Id: language_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/install_wizard_page.class.php';
/**
 * This form can be used to let the user select the action.
 */
class PreconfiguredInstallWizardPage extends InstallWizardPage
{

    function get_title()
    {
        return Translation :: get('SelectConfigurationFile');
    }

    function get_info()
    {
        return Translation :: get('SelectConfigurationFileDescription');
    }

    function buildForm()
    {
        $this->_formBuilt = true;
        
        $this->addElement('category', Translation :: get('ConfigurationFile'));
        $this->addElement('file', 'config_file', Translation :: get('ConfigurationFile'));
        $this->addRule('config_file', Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('category');
        
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', '_qf_page_language_jump', Translation :: get('Back'), array('class' => 'normal previous'));
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Finish'), array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
    }
}
?>
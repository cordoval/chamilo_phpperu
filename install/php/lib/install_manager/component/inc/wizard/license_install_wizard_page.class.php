<?php
/**
 * $Id: license_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/install_wizard_page.class.php';
/**
 * Class for license page
 * Displays the GNU GPL license that has to be accepted to install Chamilo.
 */
class LicenseInstallWizardPage extends InstallWizardPage
{

    function get_title()
    {
        return Translation :: get('Licence');
    }

    function get_info()
    {
        return Translation :: get('ChamiloLicenseInfo');
    }

    function buildForm()
    {
        $this->set_lang($this->controller->exportValue('page_language', 'install_language'));
        $this->_formBuilt = true;
        $this->addElement('category', Translation :: get('Licence'));
        $this->addElement('textarea', 'license', null, array('cols' => 80, 'rows' => 20, 'disabled' => 'disabled', 'style' => 'background-color: white;'));
        $this->addElement('checkbox', 'license_accept', '', Translation :: get('IAccept'));
        $this->addRule('license_accept', Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('category');
        
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Previous'), array('class' => 'normal previous'));
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
        $this->set_form_defaults();
    }

    function set_form_defaults()
    {
        $defaults = array();
        $defaults['license'] = implode("\n", file('../documentation/license.txt'));
        $this->setDefaults($defaults);
    }
}
?>
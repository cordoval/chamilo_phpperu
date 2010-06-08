<?php
/**
 * $Id: language_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/install_wizard_page.class.php';
/**
 * This form can be used to let the user select the action.
 */
class LanguageInstallWizardPage extends InstallWizardPage
{

    function get_title()
    {
        return Translation :: get('WelcomeToChamiloInstaller');
    }

    function get_info()
    {
        return 'Please select the language you\'d like to use while installing:';
    }

    function buildForm()
    {
    	$this->_formBuilt = true;

        $this->addElement('category', Translation :: get('Language'));
        $this->addElement('select', 'install_language', Translation :: get('InstallationLanguage'), $this->get_language_folder_list());
        $this->addElement('category');

        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Start'), array('class' => 'normal next'));
        $buttons[] = $this->createElement('style_submit_button', '_qf_page_preconfigured_jump', Translation :: get('UsePredefinedConfigurationFile'), array('class' => 'normal quickstart'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
        $this->set_form_defaults();
    }

    function get_language_folder_list()
    {
        $path = dirname(__FILE__) . '/../../../../../../languages';
        $list = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);
        $language_list = array();
        foreach ($list as $index => $language)
        {
            if ($language == '.' || $language == '..' || $language == '.svn')
            {
                continue;
            }
            $language_list[$language] = Utilities :: underscores_to_camelcase_with_spaces($language);
        }
        return $language_list;
    }

    function set_form_defaults()
    {
        $defaults = array();
        $defaults['install_language'] = 'english';
        $defaults['platform_language'] = 'english';
        $this->setDefaults($defaults);
    }
}
?>
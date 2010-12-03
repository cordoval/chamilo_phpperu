<?php
namespace install;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Filesystem;
use common\libraries\Path;

use admin\PackageInfo;
use admin\Registration;

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
        return Translation :: get('SelectLanguage');
    }

    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('category', Translation :: get('Language'));
        $this->addElement('select', 'install_language', Translation :: get('InstallationLanguage'), $this->get_language_folder_list());
        $this->addElement('category');

        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Start'), array(
                'class' => 'normal next'));
//        $buttons[] = $this->createElement('style_submit_button', '_qf_page_preconfigured_jump', Translation :: get('UsePredefinedConfigurationFile'), array(
//                'class' => 'normal quickstart'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
        $this->set_form_defaults();
    }

    function get_language_folder_list()
    {
        $language_path = Path :: get_common_libraries_path() . 'resources/i18n/';
        $language_files = Filesystem :: get_directory_content($language_path, Filesystem :: LIST_FILES, false);

        $language_list = array();
        foreach ($language_files as $language_file)
        {
            $file_info = pathinfo($language_file);
            $language_info_file = $language_path . $file_info['filename'] . '.info';

            if (file_exists($language_info_file))
            {
                $package_info = PackageInfo :: factory(Registration :: TYPE_LANGUAGE, $file_info['filename'])->get_package_info();
                $language_list[$package_info['package']['extra']['isocode']] = $package_info['package']['extra']['english'];
            }
        }

        return $language_list;
    }

    function set_form_defaults()
    {
        $defaults = array();
        $defaults['install_language'] = 'en';
        $defaults['platform_language'] = 'en';
        $this->setDefaults($defaults);
    }
}
?>
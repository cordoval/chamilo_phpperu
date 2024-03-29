<?php
namespace install;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Filesystem;

use admin\PackageInfo;
use admin\Registration;

/**
 * $Id: settings_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/install_wizard_page.class.php';
/**
 * Page in the install wizard in which some config settings are asked to the
 * user.
 */
class SettingsInstallWizardPage extends InstallWizardPage
{

    function get_title()
    {
        return Translation :: get('CfgSetting');
    }

    function get_info()
    {
        return Translation :: get('ConfigSettingsInfo');
    }

    function buildForm()
    {
        $this->set_lang($this->controller->exportValue('page_language', 'install_language'));
        $this->_formBuilt = true;

        $this->addElement('category', Translation :: get('GeneralProperties'));
        $this->addElement('select', 'platform_language', Translation :: get("MainLang"), $this->get_language_folder_list());
        $this->addElement('text', 'platform_url', Translation :: get("ChamiloURL"), array('size' => '40'));
        $this->addRule('platform_url', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addRule('platform_url', 'AddTrailingSlash', 'regex', '/^.*\/$/');
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Administrator'));
        $this->addElement('text', 'admin_email', Translation :: get("AdminEmail"), array('size' => '40'));
        $this->addRule('admin_email', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addRule('admin_email', Translation :: get('WrongEmail'), 'email');
        $this->addElement('text', 'admin_surname', Translation :: get("AdminLastName"), array('size' => '40'));
        $this->addRule('admin_surname', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('text', 'admin_firstname', Translation :: get("AdminFirstName"), array('size' => '40'));
        $this->addRule('admin_firstname', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('text', 'admin_phone', Translation :: get("AdminPhone"), array('size' => '40'));
        $this->addElement('text', 'admin_username', Translation :: get("AdminLogin"), array('size' => '40'));
        $this->addRule('admin_username', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('text', 'admin_password', Translation :: get("AdminPass"), array('size' => '40'));
        $this->addRule('admin_password', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Platform'));
        $this->addElement('text', 'platform_name', Translation :: get("CampusName"), array('size' => '40'));
        $this->addRule('platform_name', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('text', 'organization_name', Translation :: get("InstituteShortName"), array('size' => '40'));
        $this->addRule('organization_name', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('text', 'organization_url', Translation :: get("InstituteURL"), array('size' => '40'));
        $this->addRule('organization_url', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Security'));
        $self_reg[] = $this->createElement('radio', 'self_reg', null, Translation :: get('ConfirmYes', null, Utilities :: COMMON_LIBRARIES), 1);
        $self_reg[] = $this->createElement('radio', 'self_reg', null, Translation :: get('AfterApproval'), 2);
        $self_reg[] = $this->createElement('radio', 'self_reg', null, Translation :: get('ConfirmNo', null, Utilities :: COMMON_LIBRARIES), 0);
        $this->addGroup($self_reg, 'self_reg', Translation :: get("AllowSelfReg"), '&nbsp;', false);

        $dir = Path :: get_library_path() . 'hashing';
        $folders = Filesystem :: get_directory_content($dir, Filesystem :: LIST_DIRECTORIES, false);

        foreach ($folders as $folder)
        {
            if (substr($folder, 0, 1) == '.')
                continue;

            $active_folders[$folder] = $folder;
        }

        $this->addElement('select', 'hashing_algorithm', Translation :: get('HashingAlgorithm'), $active_folders);
        $this->addElement('category');

        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal previous'));
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('submit'));
        $this->set_form_defaults();
    }

    function set_form_defaults()
    {
        $defaults = array();
        $defaults['platform_language'] = $this->controller->exportValue('page_language', 'install_language');
        $urlAppendPath = str_replace('/install/index.php', '', $_SERVER['PHP_SELF']);
        $defaults['platform_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $urlAppendPath . '/';
        $defaults['admin_email'] = $_SERVER['SERVER_ADMIN'];
        $email_parts = explode('@', $defaults['admin_email']);
        if ($email_parts[1] == 'localhost')
        {
            $defaults['admin_email'] .= '.localdomain';
        }
        $defaults['admin_surname'] = 'Doe';
        $defaults['admin_firstname'] = mt_rand(0, 1) ? 'John' : 'Jane';
        $defaults['admin_username'] = 'admin';
        $defaults['admin_password'] = substr(md5(time()), 0, 8);
        $defaults['platform_name'] = Translation :: get('MyChamilo');
        $defaults['organization_name'] = Translation :: get('Chamilo');
        $defaults['organization_url'] = 'http://www.chamilo.org';
        $defaults['self_reg'] = 0;
        $defaults['encrypt_password'] = 1;
        $defaults['hashing_algorithm'] = 'sha1';
        $this->setDefaults($defaults);
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
}
?>
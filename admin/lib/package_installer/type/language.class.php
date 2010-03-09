<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';

/**
 * $Id: language.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_installer.type
 */

class PackageInstallerLanguageType extends PackageInstallerType
{

    function install()
    {
        if ($this->verify_dependencies())
        {
            $this->get_parent()->installation_successful('dependencies', Translation :: get('LanguageDependenciesVerified'));
            
            if (! $this->add_registration())
            {
                $this->get_parent()->add_message(Translation :: get('LanguageRegistrationNotAdded'), PackageInstaller :: TYPE_WARNING);
            }
            else
            {
                $this->get_parent()->add_message(Translation :: get('LanguageRegistrationAdded'));
            }
        }
        else
        {
            return $this->get_parent()->installation_failed('dependencies', Translation :: get('PackageDependenciesFailed'));
        }
        
        //$this->cleanup();
        

        $this->get_source()->cleanup();
        
        return true;
    }

    function add_registration()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $language_name = $attributes->get_code();
        
        $registration = new Language();
        $registration->set_original_name($language_name);
        $registration->set_english_name($language_name);
        $registration->set_isocode($language_name);
        $registration->set_folder($language_name);
        $registration->set_available(1);
        
        return $registration->create();
    }
}
?>
<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_type.class.php';

class PackageUpdaterLanguageType extends PackageUpdaterType
{

    function update()
    {
        if ($this->verify_dependencies())
        {
            $this->get_parent()->update_successful('dependencies', Translation :: get('LanguageDependenciesVerified'));
            
            if (! $this->add_registration())
            {
                $this->get_parent()->add_message(Translation :: get('LanguageRegistrationNotAdded'), PackageUpdater :: TYPE_WARNING);
            }
            else
            {
                $this->get_parent()->add_message(Translation :: get('LanguageRegistrationAdded'));
            }
        }
        else
        {
            return $this->get_parent()->update_failed('dependencies', Translation :: get('PackageDependenciesFailed'));
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
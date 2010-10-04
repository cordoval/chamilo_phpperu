<?php

/**
 * $Id: content_object.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_remover.type
 */
class PackageLanguageRemover extends PackageRemover
{
    private $registration;

    function run()
    {
        $adm = AdminDataManager :: get_instance();
        $registration = $adm->retrieve_registration($this->get_package());
        $this->registration = $registration;

        // Check dependencies before doing anything at all
        if (! $this->check_dependencies())
        {
            return $this->installation_failed('failed', Translation :: get('OtherPackagesDependOnThisPackage'));
        }
        else
        {
            $this->installation_successful('dependencies', Translation :: get('NoConflictingDependencies'));
        }

        if (! $this->delete_language())
        {
            return $this->installation_failed('failed', Translation :: get('LanguageDeletionFailed'));
        }
        else
        {
            $this->installation_successful('repository', Translation :: get('LanguageSuccessfullyDeleted'));
        }

        return true;
    }
    
    function delete_language()
    {
    	$language = AdminDataManager :: get_instance()->retrieve_language_from_english_name($this->registration->get_name());
    	return $language->delete();
    }
}
?>
<?php
namespace admin;
use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Translation;
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
                $this->get_parent()->add_message(Translation :: get('ObjectNotAdded', array('OBJECT' => Translation :: get('LanguageRegistration')), Utilities :: COMMON_LIBRARIES), PackageInstaller :: TYPE_WARNING);
            }
            else
            {
                $this->get_parent()->add_message(Translation :: get('ObjectAdded', array('OBJECT' => Translation :: get('LanguageRegistration')), Utilities :: COMMON_LIBRARIES));
            }
        }
        else
        {
            return $this->get_parent()->installation_failed('dependencies', Translation :: get('PackageDependenciesFailed'));
        }

        $this->get_source()->cleanup();

        return true;
    }

    static function get_path($language_name)
    {
        return Path :: get_language_path() . $language_name . '/';
    }

    function add_registration()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();

        $registration = new Language();
        $registration->set_original_name($attributes->get_name());
        $registration->set_family($attributes->get_category());

        $extra = $attributes->get_extra();
        $registration->set_english_name($extra['english']);
        $registration->set_isocode($extra['isocode']);

        $registration->set_available(1);

        return $registration->create();
    }
}
?>
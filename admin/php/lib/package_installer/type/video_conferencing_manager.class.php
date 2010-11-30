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

class PackageInstallerVideoConferencingManagerType extends PackageInstallerType
{

    function install()
    {
        if ($this->verify_dependencies())
        {
            $this->get_parent()->installation_successful('dependencies', Translation :: get('VideoConferencingManagerDependenciesVerified'));

            if (! $this->add_registration())
            {
                $this->get_parent()->add_message(Translation :: get('ObjectNotAdded', array('OBJECT' => Translation :: get('VideoConferencingManagerRegistration')), Utilities :: COMMON_LIBRARIES), PackageInstaller :: TYPE_WARNING);
            }
            else
            {
                $this->get_parent()->add_message(Translation :: get('ObjectAdded', array('OBJECT' => Translation :: get('VideoConferencingManagerRegistration')), Utilities :: COMMON_LIBRARIES));
            }
        }
        else
        {
            return $this->get_parent()->installation_failed('dependencies', Translation :: get('PackageDependenciesFailed'));
        }

        $this->get_source()->cleanup();

        return true;
    }

    static function get_path($video_conferencing_manager_name)
    {
        return Path :: get_common_extensions_path() . 'video_conferencing_manager/implementation/' . $video_conferencing_manager_name . '/' . Path :: CLASS_PATH . '/';
    }

    function add_registration()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();

        $registration = new Registration();
        $registration->set_name($attributes->get_code());
        $registration->set_type(Registration :: TYPE_EXTERNAL_REPOSITORY_MANAGER);
        $registration->set_category($attributes->get_category());
        $registration->set_version($attributes->get_version());
        $registration->set_status(Registration :: STATUS_ACTIVE);

        return $registration->create();
    }
}
?>
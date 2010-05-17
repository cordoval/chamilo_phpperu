<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_dependency.class.php';

/**
 * $Id: settings.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer.dependency
 */

class PackageUpdaterSettingsDependency extends PackageUpdaterDependency
{

    function check($dependency)
    {
        $setting = ini_get($dependency['id']);
        $message = Translation :: get('DependencyCheckSetting') . ': ' . $dependency['id'] . '. ' . Translation :: get('Expecting') . ': ' . $dependency['value']['_content'] . ' ' . Translation :: get('Found') . ': ' . $setting;
        
        $this->add_message($message);
        return $this->compare($dependency['value']['type'], $dependency['value']['_content'], $setting);
    }
}
?>
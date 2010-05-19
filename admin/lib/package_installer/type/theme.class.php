<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';

/**
 * $Id: theme.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_installer.type
 */

class PackageInstallerThemeType extends PackageInstallerType
{

    function install()
    {
        if ($this->verify_dependencies())
        {
            $this->get_parent()->installation_successful('dependencies', Translation :: get('ThemeDependenciesVerified'));
        }
        else
        {
            return $this->get_parent()->installation_failed('dependencies', Translation :: get('PackageDependenciesFailed'));
        }
        
        $this->cleanup();
        
        return true;
    }
    
    static function get_path($theme_name)
    {
    	return Path :: get_layout_path() . $theme_name . '/';
    }
}
?>
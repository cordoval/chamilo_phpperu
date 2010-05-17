<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_type.class.php';

class PackageUpdaterThemeType extends PackageUpdaterType
{

    function update()
    {
        if ($this->verify_dependencies())
        {
            $this->get_parent()->update_successful('dependencies', Translation :: get('ThemeDependenciesVerified'));
        }
        else
        {
            return $this->get_parent()->update_failed('dependencies', Translation :: get('PackageDependenciesFailed'));
        }
        
        $this->cleanup();
        
        return true;
    }
}
?>
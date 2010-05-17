<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_dependency.class.php';

class PackageUpdaterServerDependency extends PackageUpdaterDependency
{

    function check($dependency)
    {
        $message = Translation :: get('DependencyCheckServer') . ': ' . $dependency['id'] . '. ' . Translation :: get('Expecting') . ': ' . $dependency['version']['_content'] . ' ' . Translation :: get('Found') . ': ';
        
        switch ($dependency['id'])
        {
            case 'php' :
                $message .= phpversion();
                $this->add_message($message);
                return $this->version_compare($dependency['version']['type'], $dependency['version']['_content'], phpversion());
                break;
            default :
                return true;
        }
    }
}
?>
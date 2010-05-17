<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_dependency.class.php';

abstract class PackageUpdaterType
{
    private $parent;
    private $source;

    function PackageUpdaterType($parent, $source)
    {
        $this->set_parent($parent);
        $this->source = $source;
    }

    function get_source()
    {
        return $this->source;
    }

    function get_parent()
    {
        return $this->parent;
    }

    function set_parent($parent)
    {
        $this->parent = $parent;
    }

    function add_message($message, $type = PackageUpdater :: TYPE_NORMAL)
    {
        $this->get_parent()->add_message($message, $type);
    }

    function update_failed($error_message)
    {
        $this->get_parent()->update_failed($error_message);
    }

    function update_successful($type)
    {
        $this->get_parent()->update_successful($type);
    }

    function process_result($type)
    {
        $this->get_parent()->process_result($type);
    }

    abstract function install();

    function verify_dependencies()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $dependency = unserialize($attributes->get_dependencies());
        
        
//        foreach ($dependency as $type => $dependencies)
//        {
//            $verifier = PackageUpdaterDependency :: factory($this, $type, $dependencies['dependency']);
//            if (! $verifier->verify())
//            {
//                return $this->get_parent()->update_failed('dependencies', Translation :: get('PackageDependencyFailed'));
//            }
//        }
        
        $package_update_dependencies = new PackageDependencyVerifier($this, $dependency);
        
     	if (! $verifier->verify())
        {
            return false/*$this->get_parent()->installation_failed('dependencies', Translation :: get('PackageDependencyFailed'))*/;
        }
        return true;
    }
    
	function check_other_packages()
    {
        $adm = AdminDataManager :: get_instance();
        $package = $adm->retrieve_registration($this->get_package());
        
        $condition = new NotCondition(new EqualityCondition(Registration :: PROPERTY_ID, $this->get_package()));
        $registrations = $adm->retrieve_registrations($condition);
        
        $failures = 0;
        
        while ($registration = $registrations->next_result())
        {
            $type = $registration->get_type();
            
            switch ($type)
            {
                case Registration :: TYPE_APPLICATION :
                    $info_path = Path :: get_application_path() . 'lib/' . $registration->get_name() . '/package.info';
                    break;
                case Registration :: TYPE_CONTENT_OBJECT :
                    $info_path = Path :: get_repository_path() . 'lib/content_object/' . $registration->get_name() . '/package.info';
                    break;
            }
            
            $package_data = $this->get_package_info($info_path);
            
            if ($package_data)
            {
                if (! $this->parse_packages_info($package, $package_data))
                {
                    $failures ++;
                }
            }
        }
        
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Invokes the constructor of the class that corresponds to the specified
     * type of package installer type.
     */
    static function factory($parent, $type, $source)
    {
        $class = 'PackageUpdater' . Utilities :: underscores_to_camelcase($type) . 'Type';
        require_once dirname(__FILE__) . '/type/' . $type . '.class.php';
        return new $class($parent, $source);
    }
}
?>
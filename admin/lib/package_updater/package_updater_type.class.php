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
        
        foreach ($dependency as $type => $dependencies)
        {
            $verifier = PackageUpdaterDependency :: factory($this, $type, $dependencies['dependency']);
            if (! $verifier->verify())
            {
                return $this->get_parent()->update_failed('dependencies', Translation :: get('PackageDependencyFailed'));
            }
        }
        
        return true;
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
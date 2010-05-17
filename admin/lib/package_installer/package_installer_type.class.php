<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_dependency_verifier.class.php';

/**
 * $Id: package_installer_type.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer
 */

abstract class PackageInstallerType
{
    private $parent;
    private $source;

    function PackageInstallerType($parent, $source)
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

    function add_message($message, $type = PackageInstaller :: TYPE_NORMAL)
    {
        $this->get_parent()->add_message($message, $type);
    }

    function installation_failed($error_message)
    {
        $this->get_parent()->installation_failed($error_message);
    }

    function installation_successful($type)
    {
        $this->get_parent()->installation_successful($type);
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
        $verifier = new PackageDependencyVerifier($this, $dependency);
        if (! $verifier->verify())
        {
            return false/*$this->get_parent()->installation_failed('dependencies', Translation :: get('PackageDependencyFailed'))*/;
        }
        
        return true;
    }

    /**
     * Invokes the constructor of the class that corresponds to the specified
     * type of package installer type.
     */
    static function factory($parent, $type, $source)
    {
        $class = 'PackageInstaller' . Utilities :: underscores_to_camelcase($type) . 'Type';
        require_once dirname(__FILE__) . '/type/' . $type . '.class.php';
        return new $class($parent, $source);
    }
}
?>
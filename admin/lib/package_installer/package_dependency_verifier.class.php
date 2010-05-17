<?php
require_once dirname (__FILE__) . '/../package_manager/package_dependency.class.php';
/**
 * $Id: package_dependency_verifier.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer
 */

class PackageDependencyVerifier
{
    private $dependencies;
    private $parent;

    function PackageDependencyVerifier($parent, $dependencies)
    {
        $this->parent = $parent;
        $this->dependencies = $dependencies;
    }

    function get_dependencies()
    {
        return $this->dependencies;
    }

    function get_parent()
    {
        return $this->parent;
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
        $this->get_parent()->installation_succesful($type);
    }

    function process_result($type)
    {
        $this->get_parent()->process_result($type);
    }  

    function verify()
    {
        $dependencies = $this->get_dependencies();
        
        foreach ($dependencies as $type => $dependency)
        {
            foreach($dependency['dependency'] as $detail)
            {
            	$package_dependency = PackageDependency::factory($type, $detail);
	        	if (! $package_dependency->check() && $package_dependency->is_severe())
	            {
					$messages = $package_dependency->get_messages();
					foreach($messages as $message)
					{
						$this->add_message($message);
					}
					return false;
	            }
	            else {
	            	$messages = $package_dependency->get_messages();
					foreach($messages as $message)
					{
						$this->add_message($message);
					}
	            }
            }
        }        
        return true;
    }
    
}
?>
<?php
require_once dirname(__FILE__) . '/package_dependency.class.php';
require_once dirname(__FILE__) . '/../package_installer/source/package_info/package_info.class.php';
/**
 * $Id: package_dependency_verifier.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer
 */

class PackageDependencyVerifier
{
    private $package;
    private $message_logger;
    
    const TYPE_REMOVE = 'remove';
    const TYPE_UPDATE = 'update';

    function PackageDependencyVerifier($package)
    {
        $this->package = $package;
        $this->message_logger = new MessageLogger();
    }

    function get_package()
    {
        return $this->package;
    }

    function get_message_logger()
    {
        return $this->message_logger;
    }

    function is_installable()
    {
        $dependencies = unserialize($this->get_package()->get_dependencies());
        foreach ($dependencies as $type => $dependency)
        {
            foreach ($dependency['dependency'] as $detail)
            {
                $package_dependency = PackageDependency :: factory($type, $detail);
                if (! $package_dependency->check() && $package_dependency->is_severe())
                {
                    $this->get_message_logger()->add_message($package_dependency->get_message_logger()->render());
                    return false;
                }
                else
                {
                    $this->get_message_logger()->add_message($package_dependency->get_message_logger()->render());
                }
            }
        }
        return true;
    }

    function is_updatable()
    {
        return $this->check_reliabilities(self :: TYPE_UPDATE);
    }

    function is_removable()
    {
        return $this->check_reliabilities(self :: TYPE_REMOVE);
    }

    function check_reliabilities($type)
    {
        $conditions = array();
        $conditions[] = new NotCondition(new EqualityCondition(Registration :: PROPERTY_TYPE, $this->get_package()->get_section()));
        $conditions[] = new NotCondition(new EqualityCondition(Registration :: PROPERTY_NAME, $this->get_package()->get_code()));
        $condition = new OrCondition($conditions);
        
        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition);
        
        $failures = 0;
        
        while ($registration = $registrations->next_result())
        {
            $package_info = PackageInfo :: factory($registration->get_type(), $registration->get_name());
            $package_data = $package_info->get_package();
            
            if ($package_data)
            {
                switch ($this->get_package()->get_section())
                {
                    case Registration :: TYPE_APPLICATION :
                        $dependency_type = PackageDependency :: TYPE_APPLICATIONS;
                        break;
                    case Registration :: TYPE_CONTENT_OBJECT :
                        $dependency_type = PackageDependency :: TYPE_CONTENT_OBJECTS;
                        break;
                    default :
                        return true;
                }
                
                $dependencies = unserialize($package_data->get_dependencies());
                
                if (isset($dependencies[$dependency_type]))
                {
                    foreach ($dependencies[$dependency_type]['dependency'] as $dependency)
                    {
                        if ($dependency['id'] === $this->get_package()->get_code())
                        {
                            if ($type == self :: TYPE_REMOVE)
                            {
                                $message = Translation :: get('PackageDependency') . ': <em>' . $package_data->get_name() . ' (' . $package_data->get_code() . ')</em>';
                                $this->get_message_logger()->add_message($message);
                                $failures ++;
                            }
                            elseif ($type == self :: TYPE_UPDATE)
                            {
                            	$package_dependency = PackageDependency::factory($dependency_type, $dependency);
                            	$result = PackageDependency::version_compare($package_dependency->get_operator(), $package_dependency->get_version_number(),  $this->get_package()->get_version());
                            	$message = '<em>' . $package_data->get_name() . ' (' . $package_data->get_code() . ') ' . $package_dependency->get_operator_name($package_dependency->get_operator()) . ' ' . $package_dependency->get_version_number() .'</em>';
                            	
                            	if (! $result && $package_dependency->is_severe())
                            	{
                            		$failures ++;
                            		$this->get_message_logger()->add_message($message, MessageLogger::TYPE_ERROR);
                            	}
                            	elseif (! $result && ! $package_dependency->is_severe())
                            	{
                            		$this->get_message_logger()->add_message($message, MessageLogger::TYPE_WARNING);
                            	}
                            	else {
                            		$this->get_message_logger()->add_message($message);
                            	}
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                }
            }
        }
        
        if ($failures > 0)
        {
            $message = Translation :: get('VerificationFailed');
            $this->get_message_logger()->add_message($message, MessageLogger::TYPE_ERROR);
        	return false;
        }
        else
        {
        	$message = Translation :: get('VerificationSuccess');
            $this->get_message_logger()->add_message($message, MessageLogger::TYPE_CONFIRM);
            return true;
        }
    }
}
?>
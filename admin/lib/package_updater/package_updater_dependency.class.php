<?php

class PackageUpdaterDependency
{
    private $dependencies;
    private $parent;

    function PackageUpdaterDependency($parent, $dependencies)
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
        $this->get_parent()->update_succesful($type);
    }

    function process_result($type)
    {
        $this->get_parent()->process_result($type);
    }

    static function check_other_packages($package)
    {
        $adm = AdminDataManager :: get_instance();
        
        $condition = new NotCondition(new EqualityCondition(Registration :: PROPERTY_ID, $package->get_id()));
        $registrations = $adm->retrieve_registrations($condition);
        
        $failures = 0;
        $messages = array();
        while ($registration = $registrations->next_result())
        {
            $package_data = PackageInfo :: factory($registration->get_type(), $registration->get_name());
            
            $package_data = $package_data->get_package_info();
            
            if ($package_data)
            {
                $type = $package->get_type();

                switch ($type)
                {
                    case Registration :: TYPE_APPLICATION :
                        $dependency_type = 'applications';
                        break;
                    case Registration :: TYPE_CONTENT_OBJECT :
                        $dependency_type = 'content_objects';
                        break;
                }
                
                
                
                //foreach ($package_data['package'] as $package)
               // {
                    $dependencies = $package_data['package']['dependencies'];
                    
                    if (isset($dependencies[$dependency_type]))
                    {
                        foreach ($dependencies[$dependency_type]['dependency'] as $dependency)
                        {
                        	if ($dependency['id'] === $package->get_name())
                            {
                                $message = Translation :: get('PackageDependency') . ': <em>' . $package_data['package']['name'] . ' (' . $package_data['package']['code'] . ')</em>';
                                
                                $messages[] = $message;
                                $failures ++;
                            }
                        }
                    }
                //}
            }
        }
        
        if ($failures > 0)
        {
            return $messages;
        }
        else
        {
            return true;
        }
    }

    function verify()
    {
        $dependencies = $this->get_dependencies();
        
        foreach ($dependencies as $type => $dependency)
        {
            foreach ($dependency['dependency'] as $detail)
            {
                $package_dependency = PackageDependency :: factory($type, $detail);
                if (! $package_dependency->check())
                {
                    $messages = $package_dependency->get_messages();
                    foreach ($messages as $message)
                    {
                        $this->add_message($message);
                    }
                    return false;
                }
                else
                {
                    $messages = $package_dependency->get_messages();
                    foreach ($messages as $message)
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
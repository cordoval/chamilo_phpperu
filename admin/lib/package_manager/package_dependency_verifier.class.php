<?php
require_once dirname(__FILE__) . '/package_dependency.class.php';
/**
 * $Id: package_dependency_verifier.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer
 */

class PackageDependencyVerifier
{
    private $package;
    private $message_logger;

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

    }

    function is_removable()
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
                            $message = Translation :: get('PackageDependency') . ': <em>' . $package_data->get_name() . ' (' . $package_data->get_code() . ')</em>';
                            $this->get_message_logger()->add_message($message);
                            $failures ++;
                        }
                    }
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
}
?>
<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_dependency.class.php';

class PackageUpdaterApplicationsDependency extends PackageUpdaterDependency
{
    function check($dependency)
    {
        $message = Translation :: get('DependencyCheckApplication') . ': ' . Translation :: get(Utilities :: underscores_to_camelcase($dependency['id'])) . ', ' . Translation :: get('Version') . ': ' . $dependency['version']['_content'] . ' ' . Translation :: get('Found') . ': ';
        
        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $dependency['id']);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
        $condition = new AndCondition($conditions);
        
        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition, array(), 0, 1);
        
        if ($registrations->size() === 0)
        {
            $message .= '--' . Translation :: get('Nothing') . '--';
            $this->add_message($message);
            return false;
        }
        else
        {
            $registration = $registrations->next_result();
            
            $application_version = $this->version_compare($dependency['version']['type'], $dependency['version']['_content'], $registration->get_version());
            if (! $application_version)
            {
                $message .= '--' . Translation :: get('WrongVersion') . '--';
                $this->add_message($message);
                $this->add_message(Translation :: get('DependencyApplicationWrongVersion'), PackageUpdater :: TYPE_WARNING);
                return false;
            }
            else
            {
                if (! $registration->is_active())
                {
                    $message .= '--' . Translation :: get('InactiveApplication') . '--';
                    $this->add_message($message);
                    $this->add_message(Translation :: get('DependencyActivateObjectWarning'), PackageUpdater :: TYPE_WARNING);
                }
                else
                {
                    $message .= $registration->get_version();
                    $this->add_message($message);
                }
                
                return true;
            }
        }
    }
}
?>
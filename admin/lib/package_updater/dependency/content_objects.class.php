<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_dependency.class.php';

class PackageUpdaterContentObjectsDependency extends PackageUpdaterDependency
{
    function check($dependency)
    {
        $message = Translation :: get('DependencyCheckContentObject') . ': ' . Translation :: get(Utilities :: underscores_to_camelcase($dependency['id']) . 'TypeName') . ', ' . Translation :: get('Version') . ': ' . $dependency['version']['_content'] . ' ' . Translation :: get('Found') . ': ';
        
        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $dependency['id']);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_CONTENT_OBJECT);
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
            
            $content_object_version = $this->version_compare($dependency['version']['type'], $dependency['version']['_content'], $registration->get_version());
            if (! $content_object_version)
            {
                $message .= '--' . Translation :: get('WrongVersion') . '--';
                $this->add_message($message);
                $this->add_message(Translation :: get('DependencyObjectWrongVersion'), PackageUpdater :: TYPE_WARNING);
                return false;
            }
            else
            {
                if (! $registration->is_active())
                {
                    $message .= '--' . Translation :: get('InactiveObject') . '--';
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
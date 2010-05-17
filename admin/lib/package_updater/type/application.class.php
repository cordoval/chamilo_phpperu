<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_type.class.php';

class PackageUpdaterApplicationType extends PackageUpdaterType
{

    function update()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();
        
        if ($this->verify_dependencies())
        {
            $this->get_parent()->update_successful('dependencies', Translation :: get('ApplicationDependenciesVerified'));
            $updater = Updater :: factory($application_name, array());
            
            if (! $updater->update())
            {
                return $this->get_parent()->update_failed('initilization', Translation :: get('ApplicationInitilizationFailed'));
            }
            else
            {
                $this->add_message($updater->retrieve_message());
                $this->update_successful('initilization');
            }
            
            $updater->set_message(array());
            
            if (! $updater->post_process())
            {
                return $this->get_parent()->update_failed('processing', Translation :: get('ApplicationPostProcessingFailed'));
            }
            else
            {
                $this->add_message($updater->retrieve_message());
                $this->update_successful('processing');
            }
            
            if (! $this->set_version())
            {
                $this->get_parent()->add_message(Translation :: get('ApplicationVersionNotSet'), PackageUpdater :: TYPE_WARNING);
            }
            else
            {
                $this->get_parent()->add_message(Translation :: get('ApplicationVersionSet'));
            }
            
            if (! $this->add_navigation_item())
            {
                $this->get_parent()->add_message(Translation :: get('ApplicationMenuItemNotAdded'), PackageUpdater :: TYPE_WARNING);
            }
            else
            {
                $this->get_parent()->add_message(Translation :: get('ApplicationMenuItemAdded'));
            }
        }
        else
        {
            return $this->get_parent()->update_failed('dependencies', Translation :: get('PackageDependenciesFailed'));
        }
        
        $source->cleanup();
        
        return true;
    }

    function set_version()
    {
        
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $application_name);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
        $condition = new AndCondition($conditions);
        
        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition, array(), 0, 1);
        $registration = $registrations->next_result();
        $registration->set_version($attributes->get_version());
        return $registration->update();
    }

    function add_navigation_item()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();
        
        $navigation_item = new NavigationItem();
        $navigation_item->set_title(Utilities :: underscores_to_camelcase_with_spaces($application_name));
        $navigation_item->set_application($application_name);
        $navigation_item->set_section($application_name);
        $navigation_item->set_category(0);
        return $navigation_item->create();
    }
}
?>
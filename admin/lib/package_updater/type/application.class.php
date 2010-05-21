<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_type.class.php';
require_once Path :: get_common_path() . 'updater.class.php';

class PackageUpdaterApplicationType extends PackageUpdaterType
{
	function get_version($element)
	{
		return false;
	}
	
    function update()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $application_name = $attributes->get_code();
        
        $this->get_parent()->update_successful('dependencies', Translation :: get('ApplicationDependenciesVerified'));
        
        $path = BasicApplication::get_application_path($application_name) . 'update/';
        $folders = Filesystem::get_directory_content($path, Filesystem::LIST_DIRECTORIES, false);
        $update_folders = array();
        foreach($folders as $folder)
        {
        	if (version_compare($folder, $this->get_parent()->get_registration()->get_version(), '>')
        		&& version_compare(PackageInfo :: factory(Registration :: TYPE_APPLICATION, $application_name)->get_package()->get_version(), $folder , '>='))
        	{
        		
        		$update_folders[] = $folder;
        	}
        }
        foreach($update_folders as $update_folder)
        {
        	$updater = Updater :: factory($application_name, $update_folder);
	        
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
	        
	       
	        $source->cleanup();	        
        }
        return true;
    }

    function set_version()
    {  
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $registration = $this->get_parent()->get_registration();
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
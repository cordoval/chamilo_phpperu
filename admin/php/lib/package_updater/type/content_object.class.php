<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_type.class.php';
require_once Path :: get_repository_path() . 'lib/content_object_updater.class.php';

class PackageUpdaterContentObjectType extends PackageUpdaterType
{

     function update()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $type = $attributes->get_code();
        
        $this->get_parent()->update_successful('dependencies', Translation :: get('ApplicationDependenciesVerified'));
        
        $path = Path :: get_repository_path() . 'lib/content_object/' . $type . '/update/';
        $folders = Filesystem::get_directory_content($path, Filesystem::LIST_DIRECTORIES, false);
        $update_folders = array();
        foreach($folders as $folder)
        {
        	if (version_compare($folder, $this->get_parent()->get_registration()->get_version(), '>')
        		&& version_compare(PackageInfo :: factory(Registration :: TYPE_CONTENT_OBJECT, $type)->get_package()->get_version(), $folder , '>='))
        	{
        		
        		$update_folders[] = $folder;
        	}
        }
        foreach($update_folders as $update_folder)
        {
        	$updater = ContentObjectUpdater :: factory($type, $update_folder);
	        
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
}
?>
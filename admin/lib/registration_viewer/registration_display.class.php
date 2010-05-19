<?php
require_once Path :: get_admin_path() . 'lib/package_installer/source/package_info/package_info.class.php';

class RegistrationDisplay
{
	private $object;

	function RegistrationDisplay($object)
	{
		$this->object = $object;
	}

	function get_object()
    {
    	return $this->object;
    }

    function as_html()
    {
    	$object = $this->object;
    	$package_info = PackageInfo::factory($object->get_type(), $object->get_name());
    	$package_info = $package_info->get_package();

    	$html = array();
        $html[] = $this->get_properties_table($package_info);
        $html[] = $this->get_dependencies_table($package_info);
        $html[] = $this->get_update_problems();

        return implode("\n", $html);
    }

    function get_dependencies_table($package_info)
    {
    	$html[] = '<h3>' . Translation::get('Dependencies') .  '</h3>';
    	$dependencies = unserialize($package_info->get_dependencies());
    	$html[] = '<table class="data_table data_table_no_header">';
    	foreach($dependencies as $type => $dependency)
    	{
    		$count = 0;
    		foreach($dependency['dependency'] as $detail)
    		{
	    		$package_dependency = PackageDependency::factory($type, $detail);
    			$html[] = '<tr>';
    			if ($count == 0)
	    		{
	    			$html[] = '<td class="header">' . Translation :: get(Utilities::underscores_to_camelcase($type)) . '</td>';
	    		}
	    		else
	    		{
	    			$html[] = '<td></td>';
	    		}
    			$html[] = '<td>' . $package_dependency->as_html() . '</td>';

	    		$html[] = '</tr>';
	    		$count ++;
    		}
    	}
    	$html[] = '</table><br/>';
    	return implode("\n", $html);
    }

    function get_update_problems()
    {
		$conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_CODE, $this->get_object()->get_name());
        $conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_SECTION, $this->get_object()->get_type());
        $condition = new AndCondition($conditions);
        
        $admin = AdminDataManager::get_instance();
        $order_by = new ObjectTableOrder(RemotePackage :: PROPERTY_VERSION, SORT_DESC);
        
        $package_remote = $admin->retrieve_remote_packages($condition, $order_by, null, 1);
        if ($package_remote->size() == 1)
        {
        	$package_remote = $package_remote->next_result();

	        $package_update_dependency = new PackageDependencyVerifier($package_remote);
	        $success = $package_update_dependency->is_updatable();
	        if ($success)
	        {
	        	$type = 'finished';
	        }
	        else 
	        {
	        	$type = 'failed';
	        }
	    	$html = array();
	    	$html[] = '<h3>' . Translation::get('UpdateToVersion') . $package_remote->get_version() . '</h3>';
	    	$html[] = '<div class="content_object" style="padding: 15px 15px 15px 76px; background-image: url(' . Theme :: get_image_path() . 'place_' . $type . '.png);">';
        	$html[] = '<div class="title">' . Translation :: get(DependenciesResultVerification) . '</div>';
        	$html[] = '<div class="description">';
        	$html[] = $package_update_dependency->get_message_logger()->render();
        	$html[] = '</div>';
        	$html[] = '</div>';
	    	return implode("\n", $html);
        }
    }


	function get_properties_table($package_info)
    {
    	$html = array();
    	$html[] = '<table class="data_table data_table_no_header">';
    	$properties = $package_info->get_default_property_names();
    	foreach($properties as $property)
    	{
    		$value = $package_info->get_default_property($property);
    		if (!empty($value) && $property !== RemotePackage::PROPERTY_DEPENDENCIES)
    		{
    			$html[] = '<tr><td class="header">' . Translation :: get(Utilities::underscores_to_camelcase($property)) . '</td><td>' . $value . '</td></tr>';
    		}
    	}
    	$html[] = '</table><br/>';


    	return implode("\n", $html);
    }


}
?>
<?php
require_once Path::get_admin_path() . 'lib/package_installer/source/package_info/package_info.class.php';
require_once dirname(__FILE__) . '/../package_updater/package_updater_dependency.class.php';
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
    	$html[] = '</table>';
    	return implode("\n", $html);
    }
    
    function get_update_problems()
    {
    	$html = array();
    	$html[] = '<table class="data_table data_table_no_header">';
    	var_dump(PackageUpdaterDependency::check_other_packages($this->get_object()));
    	$html[] = implode('<br/>', PackageUpdaterDependency::check_other_packages($this->get_object()));
    	$html[] = '</table>';
    	return implode("\n", $html);
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
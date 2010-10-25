<?php
require_once CoreApplication :: get_application_class_lib_path('rights') . 'rights_utilities.class.php';

class GradebookRights
{
	const VIEW_RIGHT	= '1';
	const ADD_RIGHT		= '2';
	const EDIT_RIGHT	= '3';
	const DELETE_RIGHT	= '4';
	
	const LOCATION_BROWSER = 1;
	const LOCATION_HOME = 2;
	const LOCATION_VIEWER = 3;
	
	function get_available_rights()
	{
	    $reflect = new ReflectionClass('GradebookRights');
	    
	    $rights = $reflect->getConstants();
	    
	    foreach($rights as $key => $right)
		{
			if(substr(strtolower($key), 0, 8) == 'location')
			{
				unset($rights[$key]);
			}
		}          
	    
	    return $rights;
	}
	
	function is_allowed($right, $location, $type)
	{
		return RightsUtilities :: is_allowed($right, $location, $type, GradebookManager :: APPLICATION_NAME);
	}
}
?>
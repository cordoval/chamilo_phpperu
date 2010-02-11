<?php
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_application_path() . 'lib/gradebook/gradebook_manager/gradebook_manager.class.php';

class GradebookRights
{
	const VIEW_RIGHT	= '1';
	const ADD_RIGHT		= '2';
	const EDIT_RIGHT	= '3';
	const DELETE_RIGHT	= '4';
	
	function get_available_rights()
	{
	    $reflect = new ReflectionClass('GradebookRights');
	    return $reflect->getConstants();
	}
	
	function is_allowed($right, $location, $type)
	{
		return RightsUtilities :: is_allowed($right, $location, $type, GradebookManager :: APPLICATION_NAME);
	}
}
?>
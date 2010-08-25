<?php

/**
 * $Id: tracking_rights.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package tracking
 */


require_once Path :: get_tracking_path() . 'lib/tracking_manager/tracking_manager.class.php';

class TrackingRights
{
    const VIEW_RIGHT = '1';
    const EDIT_RIGHT = '2';
    
    const TREE_TYPE_TRACKING = 1;
    const TYPE_EVENT = 2;
    
    function get_available_rights()
    {
        $reflect = new ReflectionClass('TrackingRights');

	    $rights = $reflect->getConstants();

	    foreach($rights as $key => $right)
		{
			if(substr(strtolower($key), -5) != 'right')
			{
				unset($rights[$key]);
			}
		}

	    return $rights;
    }

    static function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, TrackingManager :: APPLICATION_NAME);
    }
    
	static function create_location_in_tracking_subtree($name, $identifier, $parent)
    {
    	return RightsUtilities :: create_location($name, TrackingManager :: APPLICATION_NAME, self :: TYPE_EVENT, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_TRACKING);
    }
    
    static function get_tracking_subtree_root()
    {
    	return RightsUtilities :: get_root(TrackingManager :: APPLICATION_NAME, self :: TREE_TYPE_TRACKING, 0);
    }
    
	static function get_tracking_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(TrackingManager :: APPLICATION_NAME, self :: TREE_TYPE_TRACKING, 0);
    }
    
    static function get_location_id_by_identifier_from_tracking_subtree($identifier)
    {
    	return RightsUtilities :: get_location_id_by_identifier(TrackingManager :: APPLICATION_NAME, self :: TYPE_EVENT, $identifier, 0, self :: TREE_TYPE_TRACKING);
    }
    
	static function get_location_by_identifier_from_tracking_subtree($identifier)
    {
    	return RightsUtilities :: get_location_by_identifier(TrackingManager :: APPLICATION_NAME, self :: TYPE_EVENT, $identifier, 0, self :: TREE_TYPE_TRACKING);
    }
    
	static function is_allowed_in_tracking_subtree($right, $location, $type = self :: TYPE_EVENT)
    {
    	 return RightsUtilities :: is_allowed($right, $location, $type, TrackingManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_TRACKING);
    }
    
    static function create_tracking_subtree_root_location()
    {
    	return RightsUtilities :: create_location('tracking_tree', TrackingManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_TRACKING);
    }
}
?>
<?php
/**
 * $Id: reservations_rights.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
require_once Path :: get_application_path() . 'lib/reservations/reservations_manager/reservations_manager.class.php';

class ReservationsRights
{
    const VIEW_RIGHT = '1';
    const ADD_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';
    const MANAGE_CATEGORIES_RIGHT = '5';
    const MAKE_RESERVATION_RIGHT = '6';
    
    const TREE_TYPE_RESERVATIONS = 1;
    const TYPE_CATEGORY = 1;
    const TYPE_ITEM = 2;

    function get_available_rights()
    {
        $reflect = new ReflectionClass('UserRights');

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

    function is_allowed($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, ReservationsManager :: APPLICATION_NAME);
    }

    function get_location_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_by_identifier(ReservationsManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_location_id_by_identifier($type, $identifier)
    {
        return RightsUtilities :: get_location_id_by_identifier(ReservationsManager :: APPLICATION_NAME, $type, $identifier);
    }

    function get_root_id()
    {
        return RightsUtilities :: get_root_id(ReservationsManager :: APPLICATION_NAME);
    }

    function get_root()
    {
        return RightsUtilities :: get_root(ReservationsManager :: APPLICATION_NAME);
    }

    function create_location($name, $type = 'root', $identifier = 0, $inherit = 0, $parent = 0)
    {
        return RightsUtilities :: create_location($name, ReservationsManager :: APPLICATION_NAME, $type, $identifier, $inherit, $parent);
    }
    
	static function create_location_in_reservations_subtree($name, $type, $identifier, $parent)
    {
    	return RightsUtilities :: create_location($name, ReservationsManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_RESERVATIONS);
    }
    
    static function get_reservations_subtree_root()
    {
    	return RightsUtilities :: get_root(ReservationsManager :: APPLICATION_NAME, self :: TREE_TYPE_RESERVATIONS);
    }
    
	static function get_reservations_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(ReservationsManager :: APPLICATION_NAME, self :: TREE_TYPE_RESERVATIONS);
    }
    
    static function get_location_id_by_identifier_from_reservations_subtree($type, $identifier)
    {
    	return RightsUtilities :: get_location_id_by_identifier(ReservationsManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_RESERVATIONS);
    }
    
	static function get_location_by_identifier_from_reservations_subtree($type, $identifier)
    {
    	return RightsUtilities :: get_location_by_identifier(ReservationsManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_RESERVATIONS);
    }
    
	static function is_allowed_in_reservations_subtree($right, $location, $type)
    {
    	 return RightsUtilities :: is_allowed($right, $location, $type, ReservationsManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_RESERVATIONS);
    }
}
?>
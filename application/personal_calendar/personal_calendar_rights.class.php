<?php
require_once dirname(__FILE__) . '/personal_calendar_manager/personal_calendar_manager.class.php';

class PersonalCalendarRights extends RightsUtilities
{

    const RIGHT_SHARE = '1';
    const RIGHT_PUBLISH = '2';

    const TREE_TYPE_PERSONAL_CALENDAR = 0;
    const TYPE_PERSONAL_CALENDAR = 0;


    static function get_available_rights()
    {
        return parent :: get_available_rights(PersonalCalendarManager:: APPLICATION_NAME);
    }

    static function get_available_types()
    {
        return parent :: get_available_types(PersonalCalendarManager :: APPLICATION_NAME);
    }

    static function is_allowed($right, $location)
    {
        return RightsUtilities :: is_allowed($right, $location, self :: TYPE_PERSONAL_CALENDAR, PersonalCalendarManager :: APPLICATION_NAME);
    }

    static function create_location_in_personal_calendar_subtree($name, $identifier, $parent, $tree_identifier = 0)
    {
        return RightsUtilities :: create_location($name, PersonalCalendarManager :: APPLICATION_NAME, self :: TYPE_PERSONAL_CALENDAR, $identifier, 1, $parent, 0, $tree_identifier, self :: TREE_TYPE_PERSONAL_CALENDAR);
    }

    static function get_personal_calendar_subtree_root($tree_identifier = 0)
    {
        return RightsUtilities :: get_root(PersonalCalendarManager :: APPLICATION_NAME, self :: TREE_TYPE_PERSONAL_CALENDAR, $tree_identifier);
    }

    static function get_personal_calendar_subtree_root_id($tree_identifier = 0)
    {
        return RightsUtilities :: get_root_id(PersonalCalendarManager :: APPLICATION_NAME, self :: TREE_TYPE_PERSONAL_CALENDAR, $tree_identifier);
    }

    static function get_location_id_by_identifier_from_personal_calendar_subtree($identifier, $tree_identifier = 0)
    {
        return RightsUtilities :: get_location_id_by_identifier(PersonalCalendarManager :: APPLICATION_NAME, self :: TYPE_PERSONAL_CALENDAR, $identifier, $tree_identifier, self :: TREE_TYPE_PERSONAL_CALENDAR);
    }

    static function is_allowed_in_personal_calendar_subtree($right, $location, $tree_identifier = 0)
    {
        return RightsUtilities :: is_allowed($right, $location, self :: TYPE_PERSONAL_CALENDAR, PersonalCalendarManager :: APPLICATION_NAME, null, $tree_identifier, self :: TREE_TYPE_PERSONAL_CALENDAR);
    }

    static function get_location_by_identifier_from_personal_calendar_subtree($identifier, $tree_identifier = 0)
    {
        return RightsUtilities :: get_location_by_identifier(PersonalCalendarManager :: APPLICATION_NAME, self :: TYPE_PERSONAL_CALENDAR, $identifier, $tree_identifier, self :: TREE_TYPE_PERSONAL_CALENDAR);
    }

}

?>

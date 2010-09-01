<?php

require_once dirname(__FILE__) . '/personal_messenger_manager/personal_messenger_manager.class.php';

class PersonalMessengerRights extends RightsUtilities
{

    const RIGHT_BROWSE = '1';

    const TREE_TYPE_PERSONAL_MESSENGER = 0;
    const TYPE_PERSONAL_MESSENGER = 0;


    static function get_available_rights()
    {
        return parent :: get_available_rights(PersonalMessengerManager:: APPLICATION_NAME);
    }

    static function get_available_types()
    {
        return parent :: get_available_types(PersonalMessengerManager :: APPLICATION_NAME);
    }

    static function get_personal_messenger_subtree_root($tree_identifier = 0)
    {
        return RightsUtilities :: get_root(PersonalMessengerManager :: APPLICATION_NAME, self :: TREE_TYPE_PERSONAL_MESSENGER, $tree_identifier);
    }

    static function is_allowed_in_personal_messenger_subtree($right, $location, $tree_identifier = 0)
    {
        return RightsUtilities :: is_allowed($right, $location, self :: TYPE_PERSONAL_MESSENGER, PersonalMessengerManager :: APPLICATION_NAME, null, $tree_identifier, self :: TREE_TYPE_PERSONAL_MESSENGER);
    }
}
?>

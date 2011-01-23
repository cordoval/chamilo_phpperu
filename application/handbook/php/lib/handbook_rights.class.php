<?php

namespace application\handbook;

use rights\RightsUtilities;

require_once dirname(__FILE__) . '/handbook_manager/handbook_manager.class.php';

class HandbookRights
{
    const PUBLISH_RIGHT = '1';
    const EDIT_RIGHT = '2';
    const VIEW_RIGHT = '3';
    const DELETE_PUBLICATION_RIGHT = '4';
    const CHANGE_RIGHTS_RIGHT = '5';

    //TODO: how are type ints determined?
    const TREE_TYPE_HANDBOOK = '55';
    
    
    static function get_available_rights_for_publications()
    {
    	return array('Edit' => self :: EDIT_RIGHT, 'View' => self :: VIEW_RIGHT, 'Delete Publication' => self::DELETE_PUBLICATION_RIGHT, 'Change Rights' => self::CHANGE_RIGHTS_RIGHT);
    }

     static function get_available_rights_for_application()
    {
    	return array('Publish' => self :: PUBLISH_RIGHT);
    }
    
    
    static function create_location_in_handbooks_subtree($identifier)
    {
        $name = 'handbook_publication';
        $type = self::TREE_TYPE_HANDBOOK;
        $parent = self::get_handbooks_subtree_root_id();

    	return RightsUtilities :: create_location($name, HandbookManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_HANDBOOK);
    }
    
    static function get_handbooks_subtree_root()
    {
    	return RightsUtilities :: get_root(HandbookManager :: APPLICATION_NAME);
    }
    
    static function get_handbooks_subtree_root_id()
    {
    	return RightsUtilities :: get_root_id(HandbookManager :: APPLICATION_NAME);
    }
    
    static function get_location_id_by_identifier_from_handbooks_subtree($identifier)
    {
    	return RightsUtilities :: get_location_id_by_identifier(HandbookManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_HANDBOOK);
    }
    
    static function get_location_by_identifier_from_handbooks_subtree($identifier)
    {
    	return RightsUtilities :: get_location_by_identifier(HandbookManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_HANDBOOK);
    }
    
    static function is_allowed_in_handbooks_subtree($right, $identifier, $user_id)
    {
        $type = self::TREE_TYPE_HANDBOOK;
    	 return RightsUtilities :: is_allowed($right, $identifier, $type, HandbookManager :: APPLICATION_NAME, $user_id, 0, $type);
    }
    
    static function create_handbooks_subtree_root_location()
    {
    	return RightsUtilities :: create_location('handbooks_tree', HandbookManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_HANDBOOK);
    }
}
?>
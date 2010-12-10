<?php
namespace application\phrases;

use rights\RightsUtilities;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesRights
{
    const PUBLISH_RIGHT = '1';
    const VIEW_RESULTS_RIGHT = '2';

    const TREE_TYPE_PHRASES = 1;
    const TYPE_CATEGORY = 1;
    const TYPE_PUBLICATION = 2;

    static function get_available_rights_for_publications()
    {
        return array('ViewResults' => self :: VIEW_RESULTS_RIGHT);
    }

    static function get_available_rights_for_categories()
    {
        return array('Publish' => self :: PUBLISH_RIGHT);
    }

    static function create_location_in_phrasess_subtree($name, $identifier, $parent, $type)
    {
        return RightsUtilities :: create_location($name, PhrasesManager :: APPLICATION_NAME, $type, $identifier, 1, $parent, 0, 0, self :: TREE_TYPE_PHRASES);
    }

    static function get_phrasess_subtree_root()
    {
        return RightsUtilities :: get_root(PhrasesManager :: APPLICATION_NAME, self :: TREE_TYPE_PHRASES, 0);
    }

    static function get_phrasess_subtree_root_id()
    {
        return RightsUtilities :: get_root_id(PhrasesManager :: APPLICATION_NAME, self :: TREE_TYPE_PHRASES, 0);
    }

    static function get_location_id_by_identifier_from_phrasess_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_id_by_identifier(PhrasesManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_PHRASES);
    }

    static function get_location_by_identifier_from_phrasess_subtree($identifier, $type)
    {
        return RightsUtilities :: get_location_by_identifier(PhrasesManager :: APPLICATION_NAME, $type, $identifier, 0, self :: TREE_TYPE_PHRASES);
    }

    static function is_allowed_in_phrasess_subtree($right, $location, $type)
    {
        return RightsUtilities :: is_allowed($right, $location, $type, PhrasesManager :: APPLICATION_NAME, null, 0, self :: TREE_TYPE_PHRASES);
    }

    static function create_phrasess_subtree_root_location()
    {
        return RightsUtilities :: create_location('phrasess_tree', PhrasesManager :: APPLICATION_NAME, 0, 0, 0, 0, 0, 0, self :: TREE_TYPE_PHRASES);
    }
}
?>
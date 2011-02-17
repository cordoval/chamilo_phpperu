<?php

namespace common\libraries;

use repository\ContentObject;
use repository\RepositoryDataManager;

/**
 * Base class for blocks connectors. Contains utility functions.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@gmail.com
 */
class BlockConnectorBase {
    
    /**
     * Returns a list of objects for the specified types.
     *
     * @param array $types
     * @return array
     */
    static function get_objects($types) {
        $result = array();

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());

        $types_condition = array();
        foreach ($types as $type) {
            $types_condition[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
        }
        $conditions[] = new OrCondition($types_condition);
        $condition = new AndCondition($conditions);

        $rdm = RepositoryDataManager :: get_instance();
        $objects = $rdm->retrieve_content_objects($condition);

        if ($objects->size() == 0) {
            $result[0] = Translation :: get('CreateObjectFirst');
        } else {
            while ($object = $objects->next_result()) {
                $result[$object->get_id()] = $object->get_title();
            }
        }

        return $result;
    }
}
?>

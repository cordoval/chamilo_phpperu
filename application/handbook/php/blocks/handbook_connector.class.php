<?php

namespace application\handbook;

use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use repository\content_object\rss_feed\RssFeed;
use repository\content_object\link\Link;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use repository\ContentObject;
use repository\RepositoryDataManager;

require_once dirname(__FILE__) . '/type/display.class.php';

/**
 * Helper class for handbook blocks.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 * @package handbook.block
 * @author Hans De Bisschop
 */
class HandbookBlockConnector {

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

    /**
     * Returns a list of objects that can be linked to a handbook block.
     *
     * @return array
     */
    function get_handbook_objects() {
        return self::get_objects(HandbookDisplay::get_supported_types());
    }


}

?>
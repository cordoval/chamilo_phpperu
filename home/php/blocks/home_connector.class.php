<?php

namespace home;

use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use repository\content_object\rss_feed\RssFeed;
use repository\content_object\link\Link;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use repository\ContentObject;
use repository\RepositoryDataManager;
use repository\RepositoryBlockConnector;
use common\libraries\CoreApplication;
use common\libraries\Path;
use common\libraries\BlockConnectorBase;

require_once dirname(__FILE__) . '/type/static.class.php';
require_once dirname(__FILE__) . '/type/external.class.php';
require_once Path::get_common_libraries_class_path() . 'block_connector_base.class.php';

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * 
 * @author Hans De Bisschop
 */
class HomeBlockConnector extends BlockConnectorBase{


    function get_external_objects(){
        return self::get_objects(HomeExternal::get_supported_types());
    }

    /**
     * Returns a list of objects that can be linked to a static block.
     *
     * @return array
     */
    function get_static_objects() {
        $result = array();

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());

        $types = HomeStatic::get_supported_types();
        $types_condition = array();
        foreach($types as $type){
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
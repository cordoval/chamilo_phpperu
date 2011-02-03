<?php

namespace repository;

use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use repository\content_object\rss_feed\RssFeed;
use repository\content_object\link\Link;
use common\libraries\AndCondition;
use common\libraries\OrCondition;

require_once dirname(__FILE__) . '/type/streaming.class.php';

/**
 * $Id: repository_connector.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.block.connectors
 */

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * @author Hans De Bisschop
 */
class RepositoryBlockConnector {

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

    function get_rss_feed_objects() {
        $options = array();
        $rdm = RepositoryDataManager :: get_instance();
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
        $objects = $rdm->retrieve_type_content_objects(RssFeed :: get_type_name(), $condition);

        if ($objects->size() == 0) {
            $options[0] = Translation :: get('CreateRssFeedFirst');
        } else {
            while ($object = $objects->next_result()) {
                $options[$object->get_id()] = $object->get_title();
            }
        }

        return $options;
    }

    function get_link_objects() {
        $options = array();
        $rdm = RepositoryDataManager :: get_instance();
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
        $objects = $rdm->retrieve_type_content_objects(Link :: get_type_name(), $condition);

        if ($objects->size() == 0) {
            $options[0] = Translation :: get('CreateLinkFirst');
        } else {
            while ($object = $objects->next_result()) {
                $options[$object->get_id()] = $object->get_title();
            }
        }

        return $options;
    }

    /**
     * Returns a list of objects that can be linked to a streaming block.
     *
     * @return array
     */
    function get_streaming_objects() {
        return self::get_objects(RepositoryStreaming::get_supported_types());
    }

}

?>
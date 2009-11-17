<?php
/**
 * $Id: block_repository_connector.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.block.connectors
 */
/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * @author Hans De Bisschop
 */

class BlockRepositoryConnector
{

    function get_rss_feed_objects()
    {
        $options = array();
        $rdm = RepositoryDataManager :: get_instance();
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
        $objects = $rdm->retrieve_type_content_objects('rss_feed', $condition);

        if ($objects->size() == 0)
        {
            $options[0] = Translation :: get('CreateRssFeedFirst');
        }
        else
        {
            while ($object = $objects->next_result())
            {
                $options[$object->get_id()] = $object->get_title();
            }
        }

        return $options;
    }
}
?>

<?php
namespace repository\content_object\forum;

use repository\RepositoryDataManager;
use common\libraries\EqualityCondition;
use common\libraries\ObjectTableDataProvider;
use common\extensions\repo_viewer\ContentObjectTableDataProvider;

/**
 * $Id: forumtabledataprovider.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.forum.inc
 */

class ForumTableDataProvider extends ContentObjectTableDataProvider
{
    private $forum;

    function __construct($forum)
    {
        $this->forum = $forum;
    }

    function get_content_objects($offset, $count, $order_property)
    {
        $dm = RepositoryDataManager :: get_instance();
        return $dm->retrieve_content_objects($this->get_condition(), array($order_property));
    }

    function get_content_object_count()
    {
        $dm = RepositoryDataManager :: get_instance();
        return $dm->count_content_objects($this->get_condition());
    }

    function get_condition()
    {
        return new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $this->forum->get_id());
    }
}
?>
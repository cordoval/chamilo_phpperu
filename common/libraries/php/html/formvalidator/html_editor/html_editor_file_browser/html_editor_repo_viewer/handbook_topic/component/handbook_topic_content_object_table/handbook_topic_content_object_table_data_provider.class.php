<?php
namespace common\libraries;

use common\extensions\repo_viewer\ContentObjectTableDataProvider;
use repository\RepositoryDataManager;
use repository\content_object\handbook_topic\HandbookTopic;

/**
 * $Id: content_object_table_data_provider.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once Path :: get_common_extensions_path() . 'repo_viewer/php/component/content_object_table/content_object_table_data_provider.class.php';
/**
 * This class represents a data provider for a publication candidate table
 */
class HandbookTopicContentObjectTableDataProvider extends ContentObjectTableDataProvider
{
    /**
     * Constructor.
     * @param int $owner The user id of the current active user.
     * @param array $types The possible types of learning objects which can be
     * selected.
     * @param string $query The search query.
     */
    function __construct($owner, $types, $query = null, $parent)
    {
        parent :: __construct($owner, $types, $query, $parent);
    }

    /*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        $dm = RepositoryDataManager :: get_instance();

        if (! $this->get_parent()->is_shared_object_browser())
        {
            return $dm->retrieve_type_content_objects(HandbookTopic :: get_type_name(), $this->get_condition(), $order_property, $offset, $count);
        }
        else
        {
            return $dm->retrieve_shared_type_content_objects(HandbookTopic :: get_type_name(), $this->get_condition(), $offset, $count, $order_property);
        }
    }

    /*
	 * Inherited
	 */
    function get_object_count()
    {
        $dm = RepositoryDataManager :: get_instance();

        if (! $this->get_parent()->is_shared_object_browser())
        {
            return $dm->count_type_content_objects(HandbookTopic::get_type_name(), $this->get_condition());
        }
        else
        {
            return $dm->count_shared_type_content_objects(HandbookTopic::get_type_name(), $this->get_condition());
        }
    }

    function get_type_conditions()
    {
        $handbook_topic_types = array();
        $handbook_topic_conditions = array();
        foreach($handbook_topic_types as $handbook_topic_type)
        {
            $handbook_topic_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.' . $handbook_topic_type, Document :: get_type_name());
        }

        return new OrCondition($handbook_topic_conditions);
    }
}
?>
<?php
namespace common\libraries;

use common\extensions\repo_viewer\ContentObjectTableDataProvider;
use repository\RepositoryDataManager;
use repository\content_object\document\Document;

/**
 * $Id: content_object_table_data_provider.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
/**
 * This class represents a data provider for a publication candidate table
 */
class FlashVideoContentObjectTableDataProvider extends ContentObjectTableDataProvider
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
            return $dm->retrieve_type_content_objects(Document :: get_type_name(), $this->get_condition(), $order_property, $offset, $count);
        }
        else
        {
            return $dm->retrieve_shared_type_content_objects(Document :: get_type_name(), $this->get_condition(), $offset, $count, $order_property);
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
            return $dm->count_type_content_objects(Document :: get_type_name(), $this->get_condition());
        }
        else
        {
            return $dm->count_shared_type_content_objects(Document :: get_type_name(), $this->get_condition());
        }
    }

    function get_type_conditions()
    {
        $flash_video_types = Document :: get_flash_video_types();
        $flash_video_conditions = array();
        foreach($flash_video_types as $flash_video_type)
        {
            $flash_video_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.' . $flash_video_type, Document :: get_type_name());
        }

        return new OrCondition($flash_video_conditions);
    }
}
?>
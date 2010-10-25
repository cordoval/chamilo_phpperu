<?php
namespace application\weblcms\tool\user;

/**
 * $Id: subscribe_group_browser_table_data_provider.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.subscribe_group_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class SubscribeGroupBrowserTableDataProvider extends ObjectTableDataProvider
{

    private $object_count;
    private $preloaded_result_set = null;
    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function SubscribeGroupBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
        $this->get_objects();
    }

    /**
     * Gets the learning objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
    	if(is_null($this->preloaded_result_set))
    	{
	        $order_property = $this->get_order_property($order_property);
	        $this->preloaded_result_set = WeblcmsDataManager::get_instance()->retrieve_course_subscribe_groups_by_right(CourseGroupSubscribeRight :: SUBSCRIBE_DIRECT, parent::get_browser()->get_course(),$this->get_condition(), $offset, $count, $order_property);
	        $this->object_count = $this->preloaded_result_set->size();
    	}
        return $this->preloaded_result_set;
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->object_count;
    }
}
?>
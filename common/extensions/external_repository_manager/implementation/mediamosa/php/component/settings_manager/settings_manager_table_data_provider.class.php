<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;
use common\libraries\ObjectTableDataProvider;
/**
 * $Id: repository_browser_table_data_provider.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class SettingsManagerTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param MediamosaExternalRepositoryManagerComponent $component
     * @param Condition $condition
     */
    function RepositoryBrowserTableDataProvider($component, $condition)
    {
    	parent :: __construct($component, $condition);
    }

    /**
     * Gets the server settings
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $dm = MediamosaExternalRepositoryDataManager :: get_instance();

        $order_property = $this->get_order_property($order_property);

        // We always use title as second sorting parameter
        //		$order_property[] = ContentObject :: PROPERTY_TITLE;


        return $dm->retrieve_external_repository_server_objects($this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        $dm = MediamosaExternalRepositoryDataManager :: get_instance();
        return $dm->count_external_repository_server_objects();
    }
}
?>
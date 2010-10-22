<?php
namespace common\extensions\rights_editor_manager;
use common\libraries\ObjectTableDataProvider;
use rights\RightsDataManager;
/**
 * $Id: location_template_browser_table_data_provider.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class LocationTemplateBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function LocationTemplateBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
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
        $order_property = $this->get_order_property($order_property);
        return RightsDataManager :: get_instance()->retrieve_rights_templates($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return RightsDataManager :: get_instance()->count_rights_templates($this->get_condition());
    }
}
?>
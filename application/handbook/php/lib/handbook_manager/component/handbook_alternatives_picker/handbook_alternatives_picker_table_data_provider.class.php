<?php
namespace application\handbook;
use common\libraries\ObjectTableDataProvider;
use common\libraries\Request;


/**
 * Data provider for a user browser table.
 *
 * This class implements some functions to allow user browser tables to
 * retrieve information about the users to display.
 */
class HandbookAlternativesPickerItemTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param HandbookPublicationManagerComponent $browser
     * @param Condition $condition
     */
    function __construct($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the users
     * @param String $user
     * @param String $category
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        //TODO: get alternatives
        $item_id = Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID);
        return HandbookManager::get_alternative_items($item_id);
//        return HandbookDataManager :: get_instance()->retrieve_published_handbooks($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of handbooks in the table
     * @return int
     */
    function get_object_count()
    {
//        return 1;
//        $condition = $this->get_condition();
        return HandbookManager::get_alternative_items($item_id);
    }
}
?>
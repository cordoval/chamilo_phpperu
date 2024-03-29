<?php 
namespace repository\content_object\survey;

use common\libraries\ObjectTableDataProvider;
use user\UserDataManager;

class SurveyUserTableDataProvider extends ObjectTableDataProvider
{
    
    private $component;

    function __construct($component, $condition)
    {
        parent :: __construct($component, $condition);
    }

    /**
     * Gets the User objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @param int $order_direction (SORT_ASC or SORT_DESC)
     * @return ResultSet A set of matching student objects.
     */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
//        $order_property = $this->get_order_property($order_property);
//        $order_direction = $this->get_order_property($order_direction);
        
        return UserDataManager :: get_instance()->retrieve_users($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }

    /**
     * Gets the number of users in the table
     * @return int
     */
    function get_object_count()
    {
        return UserDataManager :: get_instance()->count_users($this->get_condition());
    }
}
?>
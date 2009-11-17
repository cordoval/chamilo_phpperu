<?php
/**
 * $Id: reservation_browser_table_data_provider.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.reservation_browser
 */
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class ReservationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function ReservationBrowserTableDataProvider($browser, $condition)
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
        $condition = $this->create_condition();
        
        return $this->get_browser()->retrieve_reservations($condition, $offset, $count, $order_property);
    }

    function create_condition()
    {
        $condition = $this->get_condition();
        
        $now = Request :: get('time');
        if (! $now)
            $now = time();
        
        $from_date = strtotime('Last Monday', strtotime('+1 Day', strtotime(date('Y-m-d', $now))));
        $to_date = strtotime('-1 Second', strtotime('Next Week', $from_date));
        
        $db_from = Utilities :: to_db_date($from_date);
        $db_to = Utilities :: to_db_date($to_date);
        
        $item = Request :: get('item_id');
        
        $conditions[] = $condition;
        $conditions[] = ReservationsDataManager :: get_instance()->get_reservations_condition($db_from, $db_to, $item);
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        $condition = $this->create_condition();
        return $this->get_browser()->count_reservations($condition);
    }
}
?>
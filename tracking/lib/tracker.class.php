<?php
abstract class Tracker extends DataClass
{

    /**
     * inherited
     */
    function get_data_manager()
    {
        return TrackingDataManager :: get_instance();
    }

    /**
     * @deprecated Use run() instead
     */
    function track(array $parameters = array())
    {
        return $this->run($parameters);
    }

    abstract function run(array $parameters = array());

    //    abstract function validate_parameters();

    /**
     * Gets the table name for this class
     * @return string The table name of the current object
     */
    abstract static function get_table_name();

    /**
     * Write the values of the properties from the tracker to the database
     * @return boolean
     */
    function create()
    {
        return $this->get_data_manager()->create_tracker_item($this);
    }

    /**
     * Update the values of the properties from the tracker to the database
     * @return boolean
     */
    function update()
    {
        return $this->get_data_manager()->update_tracker_item(null, $this);
    }

    function delete()
    {
        $condition = new EqualityCondition(self :: PROPERTY_ID, $this->get_id());
        return $this->get_data_manager()->remove_tracker_items($this->table, $condition);
    }

    /**
     * @param Condition $condition
     * @param int $offset
     * @param int $max_objects
     * @param ObjectTableOrder $order_by
     * @param boolean $return_as_array
     * @return mixed ObjectResultSet or array The tracker data
     */
    function get_data($condition, $offset = null, $max_objects = null, $order_by = array(), $return_as_array = false)
    {
        $result = $this->get_data_manager()->retrieve_tracker_items_result_set($this->get_table_name(), $condition, $offset, $max_objects, $order_by, get_class($this));
        
        if ($return_as_array)
        {
            return $result->as_array();
        }
        else
        {
            return $result;
        }
    }

    /**
     * @param Condition $condition
     * @return int The number of tracker items
     */
    function count_data($condition)
    {
        return $this->get_data_manager()->count_tracker_items($this->get_table_name(), $condition);
    }

    /**
     * Retrieves tracker items with a given condition
     * @param Condition condition on which we want to retrieve the trackers
     * @return array Array of tracker items
     * @deprecated Use get_data(true) now
     */
    function retrieve_tracker_items($condition)
    {
        $trkdmg = TrackingDataManager :: get_instance();
        return $trkdmg->retrieve_tracker_items($this->get_table_name(), get_class($this), $condition);
    }

    /**
     * Retrieves tracker items with a given condition
     * @param Condition condition on which we want to retrieve the trackers
     * @return ObjectResultSet ResultSet of tracker items
     * @deprecated Use get_data() now
     */
    function retrieve_tracker_items_result_set($condition, $offset = null, $max_objects = null, $order_by = array())
    {
        return TrackingDataManager :: get_instance()->retrieve_tracker_items_result_set($this->get_table_name(), $condition, $offset, $max_objects, $order_by, get_class($this));
    }

    /**
     * @param Condition condition on which we want to retrieve the trackers
     * @deprecated Use count_data() now
     */
    function count_tracker_items($condition)
    {
        $trkdmg = TrackingDataManager :: get_instance();
        return $trkdmg->count_tracker_items($this->get_table_name(), $condition);
    }
}
?>
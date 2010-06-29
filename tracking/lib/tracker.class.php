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

    abstract function run(array $parameters = array());
}
?>
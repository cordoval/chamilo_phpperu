<?php
/**
 * @author Hans De Bisschop
 *
 */
abstract class AggregateTracker extends Tracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_TYPE = 'type';
    const PROPERTY_NAME = 'name';
    const PROPERTY_VALUE = 'value';

    function run(array $parameters = array())
    {
        $this->validate_parameters($parameters);

        $conditions = array();
        $conditions[] = new EqualityCondition(self :: PROPERTY_TYPE, $this->get_type());
        $conditions[] = new EqualityCondition(self :: PROPERTY_NAME, $this->get_name());
        $condtion = new AndCondition($conditions);

        $tracker_items = $this->retrieve_tracker_items($condtion);

        if (count($tracker_items) != 0)
        {
            $current_aggregrate_tracker = $tracker_items[0];
            $this->set_id($current_aggregrate_tracker->get_id());
            $this->set_value($current_aggregrate_tracker->get_value() + 1);
            return $this->update();
        }
        else
        {
            $this->set_value(1);
            return $this->create();
        }
    }

    /**
     * Get the default properties of all aggregate trackers.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TYPE, self :: PROPERTY_NAME, self :: PROPERTY_VALUE));
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }
}
?>
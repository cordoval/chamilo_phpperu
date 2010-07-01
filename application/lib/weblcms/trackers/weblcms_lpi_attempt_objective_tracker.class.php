<?php
/**
 * @package application.lib.weblcms.trackers
 */
class WeblcmsLpiAttemptObjectiveTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_LPI_VIEW_ID = 'lpi_view_id';
    const PROPERTY_OBJECTIVE_ID = 'objective_id';
    const PROPERTY_SCORE_RAW = 'score_raw';
    const PROPERTY_SCORE_MAX = 'score_max';
    const PROPERTY_SCORE_MIN = 'score_min';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    function track($parameters = array())
    {
        $this->set_lpi_view_id($parameters[self :: PROPERTY_LPI_VIEW_ID]);
        $this->set_objective_id($parameters[self :: PROPERTY_OBJECTIVE_ID]);
        $this->set_score_raw($parameters[self :: PROPERTY_SCORE_RAW]);
        $this->set_score_max($parameters[self :: PROPERTY_SCORE_MAX]);
        $this->set_score_min($parameters[self :: PROPERTY_SCORE_MIN]);
        $this->set_status($parameters[self :: PROPERTY_STATUS]);
        $this->set_display_order($parameters[self :: PROPERTY_DISPLAY_ORDER]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return aparent :: get_default_property_names(array(self :: PROPERTY_LPI_VIEW_ID, self :: PROPERTY_OBJECTIVE_ID, self :: PROPERTY_SCORE_RAW, self :: PROPERTY_SCORE_MAX, self :: PROPERTY_SCORE_MIN, self :: PROPERTY_STATUS, self :: PROPERTY_DISPLAY_ORDER));
    }

    function get_lpi_view_id()
    {
        return $this->get_default_property(self :: PROPERTY_LPI_VIEW_ID);
    }

    function set_lpi_view_id($lpi_view_id)
    {
        $this->set_default_property(self :: PROPERTY_LPI_VIEW_ID, $lpi_view_id);
    }

    function get_objective_id()
    {
        return $this->get_default_property(self :: PROPERTY_OBJECTIVE_ID);
    }

    function set_objective_id($objective_id)
    {
        $this->set_default_property(self :: PROPERTY_OBJECTIVE_ID, $objective_id);
    }

    function get_score_raw()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE_RAW);
    }

    function set_score_raw($score_raw)
    {
        $this->set_default_property(self :: PROPERTY_SCORE_RAW, $score_raw);
    }

    function get_score_max()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE_MAX);
    }

    function set_score_max($score_max)
    {
        $this->set_default_property(self :: PROPERTY_SCORE_MAX, $score_max);
    }

    function get_score_min()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE_MIN);
    }

    function set_score_min($score_min)
    {
        $this->set_default_property(self :: PROPERTY_SCORE_MIN, $score_min);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    function set_display_order($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>
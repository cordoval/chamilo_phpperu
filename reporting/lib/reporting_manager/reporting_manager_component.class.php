<?php
/**
 * $Id: reporting_manager_component.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager
 * @author Michael Kyndt
 */
/**
 * Base class for a reporting manager component.
 * A reporting manager provides different tools to the administrator.
 */
abstract class ReportingManagerComponent extends CoreApplicationComponent
{

    /**
     * Constructor
     * @param ReportingManager $reporting_manager The reporting manager which
     * provides this component
     */
    function ReportingManagerComponent($reporting_manager)
    {
        parent :: __construct($reporting_manager);
    }

    function count_reporting_template_registrations($condition = null)
    {
        return $this->get_parent()->count_reporting_template_registrations($condition);
    }

    function retrieve_reporting_template_registrations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_reporting_template_registrations($condition, $offset, $count, $order_property);
    }

    function retrieve_reporting_template_registration($reporting_template_registration_id)
    {
        return $this->get_parent()->retrieve_reporting_template_registration($reporting_template_registration_id);
    }

    function get_reporting_template_registration_viewing_url($reporting_template_registration)
    {
        return $this->get_parent()->get_reporting_template_registration_viewing_url($reporting_template_registration);
    }

    function get_reporting_template_registration_editing_url($reporting_template_registration)
    {
        return $this->get_parent()->get_reporting_template_registration_editing_url($reporting_template_registration);
    }
}
?>
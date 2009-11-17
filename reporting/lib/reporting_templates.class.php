<?php
/**
 * $Id: reporting_templates.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 * @author Michael Kyndt
 */
class ReportingTemplates
{

    /**
     * Creates a reporting template registration in the database
     * @param array $props
     * @return ReportingTemplateRegistration
     */
    public static function create_reporting_template_registration($props)
    {
        $reporting_template_registration = new ReportingTemplateRegistration();
        $reporting_template_registration->set_default_properties($props);
        if (! $reporting_template_registration->create())
        {
            return false;
        }
        return $reporting_template_registration;
    }
}
?>
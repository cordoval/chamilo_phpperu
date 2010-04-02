<?php
/**
 * $Id: reporting_template_viewer.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 * @author Michael Kyndt
 */
class ReportingTemplateViewer
{
    
    private $parent;

    public function ReportingTemplateViewer($parent)
    {
        $this->parent = $parent;
    }

    /**
     * by registration id
     * @param <type> $reporting_template_registration_id
     */
    public function show_reporting_template($reporting_template_registration_id)
    {
        $rpdm = ReportingDataManager :: get_instance();
        if (! $reporting_template_registration = $rpdm->retrieve_reporting_template_registration($reporting_template_registration_id))
        {
            Display :: error_message(Translation :: get("NotFound"));
            exit();
        }
        
        $this->show_reporting_template_by_name($reporting_template_registration->get_template());
    }

    /**
     * by class name
     * @param <type> $reporting_template_name
     */
    public function show_reporting_template_by_name($template)
    {
        $condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_TEMPLATE, $template);
        $rpdm = ReportingDataManager :: get_instance();
        $templates = $rpdm->retrieve_reporting_template_registrations($condition);
        $reporting_template_registration = $templates->next_result();
        
        //registration doesn't exist
        if (! isset($reporting_template_registration))
        {
            Display :: error_message(Translation :: get("NotFound"));
            exit();
        }
        
        //is platform template
        if ($reporting_template_registration->isPlatformTemplate() && ! $this->parent->get_user()->is_platform_admin())
        {
            Display :: error_message(Translation :: get("NotAllowed"));
            exit();
        }
        
        $application = $reporting_template_registration->get_application();
        $base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        $file = $base_path . $application . '/reporting/templates/' . Utilities :: camelcase_to_underscores($reporting_template_registration->get_template()) . '.class.php';
        require_once ($file);
        $new_template = Utilities :: underscores_to_camelcase($template);
        $temp = new $new_template($this->parent);
        //$temp = new $template($this->parent/*, $reporting_template_registration->get_id(), $params*/);
        if (Request :: get('s'))
        {
            $temp->show_reporting_block(Request :: get('s'));
        }
        $temp->add_parameters(ReportingManager::PARAM_TEMPLATE_ID,$reporting_template_registration->get_id());
        echo $temp->to_html();
    }
}
?>

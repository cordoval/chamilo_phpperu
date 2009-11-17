<?php
/**
 * $Id: reporting_template_registration_browser_table_cell_renderer.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager.component.reporting_template_registration_browser_table
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_template_registration_browser_table_column_model.class.php';
require_once Path :: get_reporting_path() . 'lib/reporting_template_registration_table/default_reporting_template_registration_table_cell_renderer.class.php';
/**
 * Cell renderer for the reporting template registration browser table
 */
class ReportingTemplateRegistrationBrowserTableCellRenderer extends DefaultReportingTemplateRegistrationTableCellRenderer
{
    /**
     * The reporting template registration browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ReportingTemplateManagerBrowserComponent $browser
     */
    function ReportingTemplateRegistrationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $reporting_template_registration)
    {
        if ($column === ReportingTemplateRegistrationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($reporting_template_registration);
        }
        
        return parent :: render_cell($column, $reporting_template_registration);
    }

    /**
     * Gets the action links to display
     * @param ReportingTemplateRegistration $reporting_template_registration The template
     * object for which the links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($reporting_template_registration)
    {
        $toolbar_data = array();
        
        //$viewing_url = ReportingManager::get_reporting_template_registration_url_content($this->browser,$reporting_template_registration->get_classname());
        $viewing_url = $this->browser->get_reporting_template_registration_viewing_url($reporting_template_registration);
        $toolbar_data[] = array('href' => $viewing_url, 'label' => Translation :: get('View'), 'img' => Theme :: get_common_image_path() . 'action_reporting.png');
        
        $editing_url = $this->browser->get_reporting_template_registration_editing_url($reporting_template_registration);
        $toolbar_data[] = array('href' => $editing_url, 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
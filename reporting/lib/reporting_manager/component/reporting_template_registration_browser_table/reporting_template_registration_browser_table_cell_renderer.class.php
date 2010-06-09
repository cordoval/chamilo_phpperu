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
    	
		$toolbar = new Toolbar(); 
        
    	$toolbar->add_item(new ToolbarItem(
    			Translation :: get('View'),
    			Theme :: get_common_image_path() . 'action_reporting.png',
    			$this->browser->get_reporting_template_registration_viewing_url($reporting_template_registration),
    			ToolbarItem :: DISPLAY_ICON
    	));
    	
    	$toolbar->add_item(new ToolbarItem(
    			Translation :: get('Edit'),
    			Theme :: get_common_image_path() . 'action_edit.png',
    			$this->browser->get_reporting_template_registration_editing_url($reporting_template_registration),
    			ToolbarItem :: DISPLAY_ICON
    	));
        
        return $toolbar->as_html();
    }
}
?>
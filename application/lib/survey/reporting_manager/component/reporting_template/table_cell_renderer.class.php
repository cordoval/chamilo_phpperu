<?php

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once Path :: get_application_path() . 'lib/survey/tables/reporting_template_table/default_reporting_template_table_cell_renderer.class.php';

class SurveyReportingTemplateTableCellRenderer extends DefaultSurveyReportingTemplateTableCellRenderer
{
    
    private $component;

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     */
    function SurveyReportingTemplateTableCellRenderer($component)
    {
        parent :: __construct();
        $this->component = $component;
    }

    // Inherited
    function render_cell($column, $reporting_template)
    {
        if ($column === SurveyReportingTemplateTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($column, $reporting_template);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
//            case ReportingTemplate :: PROPERTY_CITY_NAME :
//                $title = parent :: render_cell($column, $reporting_template);
//                $title_short = $title;
//                if (strlen($title_short) > 53)
//                {
//                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
//                }
//                return '<a href="' . htmlentities($this->browser->get_browse_reporting_templates_url($reporting_template)) . '" title="' . $title . '">' . $title_short . '</a>';
//            case ReportingTemplate :: PROPERTY_DESCRIPTION :
//                $description = strip_tags(parent :: render_cell($column, $reporting_template));
//                return Utilities :: truncate_string($description);
//            case Translation :: get('Subreporting_templates') :
//                return $reporting_template->count_children(true);
        }
        
        return parent :: render_cell($column, $reporting_template);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($column, $reporting_template)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Deactivate'), Theme :: get_common_image_path() . 'action_edit.png', $this->component->get_reporting_template_deactivate_url($reporting_template), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Activate'), Theme :: get_common_image_path() . 'action_delete.png', $this->component->get_reporting_template_activate_url($reporting_template), ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }
}
?>
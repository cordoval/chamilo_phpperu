<?php 
namespace application\survey;
use common\libraries\ObjectTableCellRenderer;

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
    function render_cell($column, $reporting_template_registration)
    {
        if ($column === SurveyReportingTemplateTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($column, $reporting_template_registration);
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
        
        return parent :: render_cell($column, $reporting_template_registration);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($column, $reporting_template_registration)
    {
        $toolbar = new Toolbar();
        
        $template = ReportingTemplate :: factory($reporting_template_registration, $this->component);
        if ($template instanceof SurveyLevelReportingTemplateInterface)
        {
            $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication(Request :: get(SurveyManager :: PARAM_PUBLICATION_ID));
            $survey = $publication->get_publication_object();
            if ($survey->has_context())
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Activate'), Theme :: get_common_image_path() . 'action_confirm.png', $this->component->get_publication_reporting_template_create_url($reporting_template_registration), ToolbarItem :: DISPLAY_ICON, true));
            }
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Activate'), Theme :: get_common_image_path() . 'action_confirm.png', $this->component->get_publication_reporting_template_create_url($reporting_template_registration), ToolbarItem :: DISPLAY_ICON, true));
        }
        
        return $toolbar->as_html();
    }
}
?>
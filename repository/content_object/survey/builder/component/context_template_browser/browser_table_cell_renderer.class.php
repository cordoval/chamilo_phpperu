<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../tables/survey_context_template_table/default_survey_context_template_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SurveyContextTemplateBrowserTableCellRenderer extends DefaultSurveyContextTemplateTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function SurveyContextTemplateBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $template)
    {
        if ($column === SurveyContextTemplateBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($template);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case SurveyContextTemplate :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $template);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                
                return '<a href="' . htmlentities($this->browser->get_template_viewing_url($template->get_id())) . '" title="' . $title . '">' . $title_short . '</a>';
            case SurveyContextTemplate :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $template));
                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }
                return Utilities :: truncate_string($description);
            case Translation :: get('SurveyPages') :
                $survey_id = $this->browser->get_root_content_object()->get_id();
                $template_id = $template->get_id();
                $conditions = array();
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $survey_id);
                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $template_id);
                $condition = new AndCondition($conditions);
                return SurveyContextDataManager :: get_instance()->count_template_rel_pages($condition);
            case Translation :: get('SubContexts') :
                return $template->count_children(false);
        }
        
        return parent :: render_cell($column, $template);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($template)
    {
        
    }
}
?>
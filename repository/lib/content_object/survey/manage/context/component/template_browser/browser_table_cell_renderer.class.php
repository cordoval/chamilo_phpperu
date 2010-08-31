<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/tables/context_template_table/default_context_template_table_cell_renderer.class.php';
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
    function render_cell($column, $context_template)
    {
        if ($column === SurveyContextTemplateBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($context_template);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case SurveyContextTemplate :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $context_template);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                
                return '<a href="' . htmlentities($this->browser->get_context_template_viewing_url($context_template)) . '" title="' . $title . '">' . $title_short . '</a>';
            case SurveyContextTemplate :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $context_template));
                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }
                return Utilities :: truncate_string($description);
            //            case Translation :: get('SurveyPages') :
            //                $survey_id = $this->browser->get_root_content_object()->get_id();
            //                $context_template_id = $context_template->get_id();
            //                $conditions = array();
            //                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $survey_id);
            //                $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $context_template_id);
            //                $condition = new AndCondition($conditions);
            //                return SurveyContextTemplateDataManager :: get_instance()->count_template_rel_pages($condition);
            case Translation :: get('Levels') :
                return $context_template->count_children(true) + 1;
        }
        
        return parent :: render_cell($column, $context_template);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($context_template)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_context_template_update_url($context_template), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_context_template_delete_url($context_template), ToolbarItem :: DISPLAY_ICON));
        return $toolbar->as_html();
    }
}
?>
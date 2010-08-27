<?php

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/tables/template_table/default_template_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object component table
 */
class SurveyTemplateTableCellRenderer extends DefaultSurveyTemplateTableCellRenderer
{
    
    private $component;
    private $context_template_id;

    /**
     * Constructor
     * @param RepositoryManagerComponent $component
     */
    function SurveyTemplateTableCellRenderer($component, $context_template_id)
    {
        parent :: __construct();
        $this->component = $component;
        $this->context_template_id = $context_template_id;
    }

    // Inherited
    function render_cell($column, $template)
    {
        if ($column === SurveyTemplateTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($template);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
        //            case SurveyTemplate :: PROPERTY_NAME :
        //                $title = parent :: render_cell($column, $template);
        //                $title_short = $title;
        //                if (strlen($title_short) > 53)
        //                {
        //                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
        //                }
        //                
        //                return '<a href="' . htmlentities($this->component->get_context_template_viewing_url($template)) . '" title="' . $title . '">' . $title_short . '</a>';
        //            case SurveyTemplate :: PROPERTY_DESCRIPTION :
        //                $description = strip_tags(parent :: render_cell($column, $template));
        //                if (strlen($description) > 175)
        //                {
        //                    $description = mb_substr($description, 0, 170) . '&hellip;';
        //                }
        //                return Utilities :: truncate_string($description);
        

        }
        
        return parent :: render_cell($column, $template);
    }

    private function get_modification_links($template)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->component->get_template_update_url($template), ToolbarItem :: DISPLAY_ICON));
        return $toolbar->as_html();
    }
}
?>
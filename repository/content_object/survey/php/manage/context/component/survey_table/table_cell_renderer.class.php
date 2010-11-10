<?php namespace repository\content_object\survey;

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/tables/survey_table/default_survey_table_cell_renderer.class.php';

class SurveyTableCellRenderer extends DefaultSurveyTableCellRenderer
{
    
    private $component;

    function SurveyTableCellRenderer($component)
    {
        parent :: __construct();
        $this->component = $component;
    
    }

    // Inherited
    function render_cell($column, $survey)
    {
        if ($column === SurveyTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($survey);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case Survey :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $survey);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                if ($survey->has_context())
                {
                    return '<a href="' . htmlentities($this->component->get_context_template_suscribe_page_browser_url($survey)) . '" title="' . $title . '">' . $title_short . '</a>';
                
                }
                else
                {
                    return $title_short;
                }
            
            case Survey :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $survey));
                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }
                return Utilities :: truncate_string($description);
        
        }
        
        return parent :: render_cell($column, $survey);
    }

    private function get_modification_links($survey)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        if (! $survey->has_context())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Add', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->component->get_subscribe_context_template_url($survey), ToolbarItem :: DISPLAY_ICON));
        
        }else{
        $toolbar->add_item(new ToolbarItem(Translation :: get('Subscribe'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->component->get_context_template_suscribe_page_browser_url($survey), ToolbarItem :: DISPLAY_ICON));
        	
        }
        return $toolbar->as_html();
    }
}
?>
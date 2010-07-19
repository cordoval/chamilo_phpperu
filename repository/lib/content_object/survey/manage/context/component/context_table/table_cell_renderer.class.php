<?php

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/tables/context_table/default_context_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object component table
 */
class SurveyContextTableCellRenderer extends DefaultSurveyContextTableCellRenderer
{
    
    private $component;

    /**
     * Constructor
     * @param RepositoryManagerComponent $component
     */
    function SurveyContextTableCellRenderer($component)
    {
        parent :: __construct();
        $this->component = $component;
    }

    // Inherited
    function render_cell($column, $context)
    {
        if ($column === SurveyContextTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($context);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
//            case SurveyContext :: PROPERTY_NAME :
//                $title = parent :: render_cell($column, $context);
//                $title_short = $title;
//                if (strlen($title_short) > 53)
//                {
//                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
//                }
//                
//                return '<a href="' . htmlentities($this->component->get_context_registration_viewing_url($context)) . '" title="' . $title . '">' . $title_short . '</a>';
//            case SurveyContext :: PROPERTY_DESCRIPTION :
//                $description = strip_tags(parent :: render_cell($column, $context));
//                if (strlen($description) > 175)
//                {
//                    $description = mb_substr($description, 0, 170) . '&hellip;';
//                }
//                return Utilities :: truncate_string($description);

        }
        
        return parent :: render_cell($column, $context);
    }
   
    private function get_modification_links($context)
    {
        
    }
}
?>
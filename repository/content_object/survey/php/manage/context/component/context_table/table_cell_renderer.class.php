<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/tables/context_table/default_context_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object component table
 */
class SurveyContextTableCellRenderer extends DefaultSurveyContextTableCellRenderer
{
    
    private $component;
    private $context_registration_id;

    /**
     * Constructor
     * @param RepositoryManagerComponent $component
     */
    function __construct($component, $context_registration_id)
    {
        parent :: __construct();
        $this->component = $component;
        $this->context_registration_id = $context_registration_id;
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
            case SurveyContext :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $context);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                
                return '<a href="' . htmlentities($this->component->get_context_view_url($context)) . '" title="' . $title . '">' . $title_short . '</a>';
        }
        
        return parent :: render_cell($column, $context);
    }

    private function get_modification_links($context)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->component->get_context_update_url($this->context_registration_id, $context), ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('View', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->component->get_context_view_url($context), ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
?>
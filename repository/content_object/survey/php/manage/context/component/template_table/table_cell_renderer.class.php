<?php
namespace repository\content_object\survey;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;
use common\libraries\AndCondition;

class SurveyTemplateTableCellRenderer extends DefaultSurveyTemplateTableCellRenderer
{
    private $component;
  
    function __construct($component)
    {
        parent :: __construct();
        $this->component = $component;
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
            case SurveyTemplate :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $template);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }

                return '<a href="' . htmlentities($this->component->get_template_viewing_url($template)) . '" title="' . $title . '">' . $title_short . '</a>';
            case SurveyTemplate :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $template));
                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }
                return Utilities :: truncate_string($description);
            //            case Translation :: get('SurveyPages') :
            //                $survey_id = $this->browser->get_root_content_object()->get_id();
            //                $template_id = $template->get_id();
            //                $conditions = array();
            //                $conditions[] = new EqualityCondition(SurveyTemplateRelPage :: PROPERTY_SURVEY_ID, $survey_id);
            //                $conditions[] = new EqualityCondition(SurveyTemplateRelPage :: PROPERTY_TEMPLATE_ID, $template_id);
            //                $condition = new AndCondition($conditions);
            //                return SurveyTemplateDataManager :: get_instance()->count_template_rel_pages($condition);
//            case Translation :: get('Levels') :
//                return $template->count_children(true) + 1;
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
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->component->get_template_update_url($template), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->component->get_template_delete_url($template), ToolbarItem :: DISPLAY_ICON));
        return $toolbar->as_html();
    }
}
?>
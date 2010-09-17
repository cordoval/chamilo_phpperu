<?php

/**
 * $Id: assessment_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_browser
 */
require_once dirname(__FILE__) . '/../../../../browser/object_publication_table/object_publication_table_cell_renderer.class.php';

/**
 * This class is a cell renderer for a publication candidate table
 */
class AssessmentCellRenderer extends ObjectPublicationTableCellRenderer
{
    /*
     * Inherited
     */

    function render_cell($column, $publication)
    {
        if ($column === ObjectPublicationTableColumnModel :: get_action_column())
        {
            $actions = $this->get_actions($publication);
            $actions->prepend_item(new ToolbarItem(Translation :: get('TakeAssessment'), Theme :: get_common_image_path() . 'action_next.png', $this->table_renderer->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON));
            return $actions->as_html();
        }

        switch ($column->get_name())
        {
            case Assessment :: PROPERTY_ASSESSMENT_TYPE :
                $type = $publication->get_content_object()->get_assessment_type_name();
                if ($publication->is_hidden())
                {
                    return '<span style="color: gray">' . $type . '</span>';
                }
                else
                {
                    return $type;
                }
        }

        return parent :: render_cell($column, $publication);
    }

}

?>
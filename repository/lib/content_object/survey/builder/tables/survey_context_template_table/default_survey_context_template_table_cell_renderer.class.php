<?php

class DefaultSurveyContextTemplateTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyContextTemplateTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $template)
    {
        switch ($column->get_name())
        {
            case SurveyContextTemplate :: PROPERTY_NAME :
                return $template->get_name();
            case SurveyContextTemplate :: PROPERTY_DESCRIPTION :
                return $template->get_description();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>
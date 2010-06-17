<?php

class DefaultSurveyContextTemplateRelPageTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyContextTemplateRelPageTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $templaterelpage)
    {
            
        $dm = SurveyContextDataManager :: get_instance();
        $page = $dm->retrieve_content_object($templaterelpage->get_page_id());
//        $template = $dm->retrieve_survey_context_template($templaterelpage->get_template_id());
        
        switch ($column->get_name())
        {
//            case SurveyContextTemplate :: PROPERTY_NAME :
//                return $template->get_name();
//            case SurveyContextTemplate :: PROPERTY_DESCRIPTION :
//                return $template->get_description();
            case SurveyPage :: PROPERTY_TITLE :
                return $page->get_title();
            case SurveyPage :: PROPERTY_DESCRIPTION :
                return $page->get_description();
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
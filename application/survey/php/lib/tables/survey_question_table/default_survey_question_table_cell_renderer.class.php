<?php

class DefaultSurveyQuestionTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyQuestionTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param SurveyQuestion $survey_question - The survey_question
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $survey_question)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                 return $survey_question->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($survey_question->get_description(), 200);
             	return $description;
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
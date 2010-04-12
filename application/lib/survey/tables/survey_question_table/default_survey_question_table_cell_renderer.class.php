<?php

require_once dirname(__FILE__) . '/../../survey_question.class.php';

class DefaultSurveyQuestionTableCellRenderer implements ObjectTableCellRenderer
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
        $content_object = $survey_question->get_publication_object();
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                 return $content_object->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($content_object->get_description(), 200);
             	return $description;
          	default :
                return '&nbsp;';
        }
    }

    private function get_date($date)
    {
        if ($date == 0)
        {
            return Translation :: get('NoDate');
        }
        else
        {
            return date("Y-m-d H:i", $date);
        
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>
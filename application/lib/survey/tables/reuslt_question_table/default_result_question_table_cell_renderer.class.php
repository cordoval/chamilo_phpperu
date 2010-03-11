<?php
/**
 * $Id: default_result_question_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.tables.survey_publication_table
 */

require_once dirname(__FILE__) . '/../../survey_publication.class.php';

/**
 * Default cell renderer for the survey_publication table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultResultQuestionTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultResultQuestionTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param SurveyPublication $survey_publication - The survey_publication
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $survey_publication)
    {
        $content_object = $survey_publication->get_publication_object();
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                
                if ($survey_publication->get_hidden())
                {
                    return '<span style="color: #999999;">' . $content_object->get_title() . '</span>';
                }
                
                return $content_object->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($content_object->get_description(), 200);
                
                if ($survey_publication->get_hidden())
                {
                    return '<span style="color: #999999;">' . $description . '</span>';
                }
                
                return $description;
            case ContentObject :: PROPERTY_TYPE :
                $type = Translation :: get($content_object->get_type());
                if ($type == 'survey')
                {
                    $type = $content_object->get_survey_type();
                }
                
                if ($survey_publication->get_hidden())
                {
                    return '<span style="color: #999999;">' . $type . '</span>';
                }
                
                return $type;
            case SurveyPublication :: PROPERTY_FROM_DATE :
                return $survey_publication->get_from_date();
            case SurveyPublication :: PROPERTY_TO_DATE :
                return $survey_publication->get_to_date();
            case SurveyPublication :: PROPERTY_PUBLISHER :
                return $survey_publication->get_publisher();
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
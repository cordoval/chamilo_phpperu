<?php
/**
 * $Id: default_survey_publication_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.tables.survey_publication_table
 */

require_once dirname(__FILE__) . '/../../survey_publication.class.php';

/**
 * Default cell renderer for the survey_publication table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultSurveyPublicationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultSurveyPublicationTableCellRenderer()
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
                $content_object = $survey_publication->get_publication_object();
                $title = $content_object->get_title();
                return $title;
            case ContentObject :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($content_object->get_description(), 200);
                return $description;
            case SurveyPublication :: PROPERTY_FROM_DATE :
                return $this->get_date($survey_publication->get_from_date());
            case SurveyPublication :: PROPERTY_TO_DATE :
                return $this->get_date($survey_publication->get_to_date());
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
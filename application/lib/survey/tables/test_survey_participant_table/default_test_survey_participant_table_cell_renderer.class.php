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
class DefaultTestSurveyParticipantTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultTestSurveyParticipantTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param SurveyPublication $survey_publication - The survey_publication
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $survey_participant_tracker)
    {
        $user_id = $survey_participant_tracker->get_user_id();
        $user = UserDataManager :: get_instance()->retrieve_user($user_id);
        
        switch ($column->get_name())
        {
            case User :: PROPERTY_USERNAME :
                return $user->get_fullname();
            case SurveyParticipantTracker :: PROPERTY_STATUS :
                return $survey_participant_tracker->get_status();
            case SurveyParticipantTracker :: PROPERTY_PROGRESS :
                return $survey_participant_tracker->get_progress();
            case SurveyParticipantTracker :: PROPERTY_START_TIME :
                return $survey_participant_tracker->get_start_time();
            case SurveyParticipantTracker :: PROPERTY_TOTAL_TIME :
                return $survey_participant_tracker->get_total_time();
            case SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME :
                return $survey_participant_tracker->get_context_name();    
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
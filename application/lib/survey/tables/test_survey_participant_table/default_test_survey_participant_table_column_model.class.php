<?php
/**
 * $Id: default_survey_publication_table_column_model.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.tables.survey_publication_table
 */
require_once dirname(__FILE__) . '/../../survey_publication.class.php';

/**
 * Default column model for the survey_publication table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultTestSurveyParticipantTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultTestSurveyParticipantTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return Array(ObjectTableColumn)
     */
    private static function get_default_columns()
    {
        
        $columns = array();
        $columns[] = new ObjectTableColumn(User :: PROPERTY_USERNAME, false);
        $columns[] = new ObjectTableColumn(SurveyParticipantTracker :: PROPERTY_STATUS, true);
        $columns[] = new ObjectTableColumn(SurveyParticipantTracker :: PROPERTY_PROGRESS, true);
        $columns[] = new ObjectTableColumn(SurveyParticipantTracker :: PROPERTY_START_TIME, true);
        $columns[] = new ObjectTableColumn(SurveyParticipantTracker :: PROPERTY_TOTAL_TIME, true);
        $columns[] = new ObjectTableColumn(SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME, true);
        
        return $columns;
    }
}
?>
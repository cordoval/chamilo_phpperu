<?php

require_once dirname(__FILE__) . '/../../survey_publication.class.php';


class DefaultParticipantTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultParticipantTableColumnModel()
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
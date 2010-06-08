<?php

require_once dirname(__FILE__) . '/../../survey_publication.class.php';


class DefaultParticipantTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultParticipantTableCellRenderer()
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
                return $this->get_date($survey_participant_tracker->get_start_time());
            case SurveyParticipantTracker :: PROPERTY_TOTAL_TIME :
                return $this->get_total_time($survey_participant_tracker->get_total_time());
            case SurveyParticipantTracker :: PROPERTY_CONTEXT_NAME :
                return $survey_participant_tracker->get_context_name();
            default :
                return '&nbsp;';
        }
    }

    private function get_total_time($s)
    {
        
        $d = intval($s / 86400);
        $s -= $d * 86400;
        
        $h = intval($s / 3600);
        $s -= $h * 3600;
        
        $m = intval($s / 60);
        $s -= $m * 60;
        
        if ($d)
            $str = $d . 'd ';
        if ($h)
            $str .= $h . 'h ';
        if ($m)
            $str .= $m . 'm ';
        if ($s)
            $str .= $s . 's';
        
        return $str;
    
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
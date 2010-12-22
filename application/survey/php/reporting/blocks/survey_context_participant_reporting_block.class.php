<?php
namespace application\survey;

use reporting\ReportingChartFormatter;
use reporting\ReportingFormatter;
use reporting\ReportingData;

use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

use repository\content_object\survey\SurveyContextRelUser;
use repository\content_object\survey\SurveyContextDataManager;
use repository\content_object\survey\SurveyContext;
use tracking\Tracker;

class SurveyContextParticipantReportingBlock extends SurveyReportingBlock
{
    const INVITED = 'invited';
    const NOTSTARTED = 'notstarted';
    const FINISHED = 'finished';
    const PARTICIPATION = 'participation';
    
    private $user_id;
    private $publication_id;
    private $context_type;

    function __construct($parent, $publication_id, $user_id, $context_type)
    {
        parent :: __construct($parent);
        $this->user_id = $user_id;
        $this->publication_id = $publication_id;
        $this->context_type = $context_type;
    }

    public function get_title()
    {
        return Translation :: get('Participation');
    }

    public function count_data()
    {
        
        return $this->create_reporting_data();
    
    }

    public function retrieve_data()
    {
        return $this->create_reporting_data();
    
    }

    private function create_reporting_data()
    {
        
        $reporting_data = new ReportingData();
        
        $invited_row = Translation :: get(self :: INVITED);
        $not_started_row = Translation :: get(self :: NOTSTARTED);
        $finished_row = Translation :: get(self :: FINISHED);
        $participated_row = Translation :: get(self :: PARTICIPATION);
        
        $rows = array($invited_row, $not_started_row, $finished_row, $participated_row);
        
        $reporting_data->set_rows($rows);
        
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_USER_ID, $this->user_id, SurveyContextRelUser :: get_table_name());
        $conditions[] = new EqualityCondition(SurveyContext :: PROPERTY_TYPE, $this->context_type, SurveyContext :: get_table_name());
        $condition = new AndCondition($conditions);
        
        $context_rel_users = SurveyContextDataManager :: get_instance()->retrieve_survey_context_rel_users($condition);
        
        while ($context_rel_user = $context_rel_users->next_result())
        {
            $categorie = $context_rel_user->get_optional_property(SurveyContext :: PROPERTY_NAME);
            $reporting_data->add_category($categorie);
            
            $conditions = array();
            
            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $this->publication_id);
            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_CONTEXT_ID, $context_rel_user->get_context_id());
            $condition = new AndCondition($conditions);
            $trackers = Tracker :: get_data(SurveyParticipantTracker :: CLASS_NAME, SurveyManager :: APPLICATION_NAME, $condition);
            
            $total_user_ids = array();
            $finished_user_ids = array();
            $not_started_user_ids = array();
            
            while ($tracker = $trackers->next_result())
            {
                $total_user_ids[] = $tracker->get_user_id();
                $status = $tracker->get_status();
                switch ($status)
                {
                    case SurveyParticipantTracker :: STATUS_FINISHED :
                        $finished_user_ids[] = $tracker->get_user_id();
                        break;
                    case SurveyParticipantTracker :: STATUS_NOTSTARTED :
                        $not_started_user_ids[] = $tracker->get_user_id();
                        break;
                    case SurveyParticipantTracker :: STATUS_STARTED :
                        $not_started_user_ids[] = $tracker->get_user_id();
                        break;
                
                }
            }
                      
            $total_user_ids = array_unique($total_user_ids);
            $finished_user_ids = array_unique($finished_user_ids);
            $not_started_user_ids = array_unique($not_started_user_ids);
            
            $invited_users = count($total_user_ids);
            $finished_users = count($finished_user_ids);
//            $not_started_users = count($not_started_user_ids);
			$not_started_users = $invited_users-$finished_users;
            $participation = number_format($finished_users / $invited_users * 100, 2);
            
            $reporting_data->add_data_category_row($categorie, $invited_row, $invited_users);
            $reporting_data->add_data_category_row($categorie, $finished_row, $finished_users);
            $reporting_data->add_data_category_row($categorie, $not_started_row, $not_started_users);
            $reporting_data->add_data_category_row($categorie, $participated_row, $participation);
        
        }
              
        return $reporting_data;
    }

    function get_application()
    {
        return SurveyManager :: APPLICATION_NAME;
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        //        $modes[ReportingChartFormatter :: DISPLAY_PIE] = Translation :: get('Chart:Pie');
        //        $modes[ReportingChartFormatter :: DISPLAY_BAR] = Translation :: get('Chart:Bar');
        //        $modes[ReportingChartFormatter :: DISPLAY_LINE] = Translation :: get('Chart:Line');
        //        $modes[ReportingChartFormatter :: DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
    }
}
?>
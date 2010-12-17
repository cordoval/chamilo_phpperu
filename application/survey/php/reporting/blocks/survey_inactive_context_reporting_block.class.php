<?php
namespace application\survey;

use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use repository\content_object\survey\SurveyContext;
use repository\content_object\survey\SurveyContextRelUser;
use repository\content_object\survey\SurveyContextDataManager;
use reporting\ReportingData;

//require_once dirname(__FILE__) . '/../survey_reporting_block.class.php';
//require_once dirname(__FILE__) . '/../../survey_manager/survey_manager.class.php';
//require_once (dirname(__FILE__) . '/../../trackers/survey_question_answer_tracker.class.php');
//require_once (dirname(__FILE__) . '/../../trackers/survey_participant_tracker.class.php');
//require_once Path :: get_repository_path() . 'lib/content_object/survey/analyzer/analyzer.class.php';

class SurveyInactiveContextReportingBlock extends SurveyReportingBlock
{
    
    private $context_template_id;
    private $user_id;

    function SurveyInactiveContextReportingBlock($parent, $context_template_id, $user_id)
    {
        parent :: __construct($parent);
        $this->context_template_id = $context_template_id;
        $this->user_id = $user_id;
     }

    public function get_title()
    {
        return Translation :: get('inactivecontext');
    }

    public function count_data()
    {
        return $this->create_reporting_data();
    
    }

    public function retrieve_data()
    {
        return $this->create_reporting_data();
    }

    function get_application()
    {
        return SurveyManager :: APPLICATION_NAME;
    }

    private function create_reporting_data()
    {
        
        //retrieve inactive contexts for specific user
        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyContextRelUser :: PROPERTY_USER_ID, $this->user_id);
        
        $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->context_template_id);
        $context_type = $context_template->get_context_type();
        $conditions[] = new EqualityCondition(SurveyContext :: PROPERTY_TYPE, $context_type, SurveyContext :: get_table_name());
        
        $conditions[] = new EqualityCondition(SurveyContext :: PROPERTY_ACTIVE, 0, SurveyContext :: get_table_name());
        $condition = new AndCondition($conditions);
               
        $dm = SurveyContextDataManager :: get_instance();
        $context_rel_users = $dm->retrieve_survey_context_rel_users($condition);
        
        $context_count = $dm->count_survey_context_rel_users($condition);
              
        $reporting_data = new ReportingData();
        
        $reporting_data->add_category('answer');
        
        $categories = array();
        $nr = 0;
        while ($context_count > 0)
        {
            $nr ++;
            $categories[] = $nr;
            $context_count --;
        }
        
        $context_row = Translation :: get('Context');
        $rows = array($context_row);
        
        $reporting_data->set_categories($categories);
        $reporting_data->set_rows($rows);
        $nr = 0;
        
        while ($context_rel_user = $context_rel_users->next_result())
        {
           	$nr ++;
            $reporting_data->add_data_category_row($nr, $context_row, $context_rel_user->get_optional_property(SurveyContext :: PROPERTY_NAME));
        }
        return $reporting_data;
    }
}

?>
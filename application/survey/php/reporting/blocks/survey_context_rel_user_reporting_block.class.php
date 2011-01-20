<?php 
namespace application\survey;

//use common\libraries\Path;
use repository\RepositoryDataManager;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\InCondition;
use common\libraries\Request;
use tracking\Tracker;
use repository\content_object\survey\SurveyContextDataManager;
use repository\content_object\survey\SurveyContextRelUser;
use repository\content_object\survey\SurveyAnalyzer;

//require_once dirname(__FILE__) . '/../survey_reporting_block.class.php';
//require_once dirname(__FILE__) . '/../../survey_manager/survey_manager.class.php';
//require_once (dirname(__FILE__) . '/../../trackers/survey_question_answer_tracker.class.php');
//require_once (dirname(__FILE__) . '/../../trackers/survey_participant_tracker.class.php');
//require_once Path :: get_repository_path() . 'lib/content_object/survey/analyzer/analyzer.class.php';

class SurveyContextRelUserReportingBlock extends SurveyReportingBlock
{
    private $publication_id;
    private $question_id;
    private $question;
    private $analyse_type;

    function __construct($parent, $complex_question_id, $publication_id, $analyse_type)
    {
        parent :: __construct($parent);
        $complex_question = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_question_id);
        $this->publication_id = $publication_id;
        $this->question = $complex_question->get_ref_object();
        $this->question_id = $this->question->get_id();
        $this->analyse_type = $analyse_type;
    }

    public function get_title()
    {
        return $this->question->get_title();
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
        //retrieve the answer trackers
        
        $context_template_id = 7; //to be implemented for dynamic context template ids
        $context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($context_template_id);
        $context_type = $context_template->get_context_type();

        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $this->question_id);
              
        //$publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID, $this->publication_id);
        
        //$user_id = $this->parent->get_user_id();
        $user_id = 5334;
        $condition = new EqualityCondition(SurveyContextRelUser :: PROPERTY_USER_ID, $user_id);
        
     	$dm = SurveyContextDataManager :: get_instance();
    	$objects = $dm->retrieve_survey_context_rel_users($condition);
    
    	while ($context = $objects->next_result())
    	{
        	 $context_ids[] = $context->get_id();
    	}
                    
        if (is_array($context_ids))
        {
            $conditions[] = new InCondition(SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID, $context_ids);
        }
        else
        {
            $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID, 0);
        }
                
        $condition = new AndCondition($conditions);
          
        $trackers = Tracker :: get_data(SurveyQuestionAnswerTracker :: CLASS_NAME, SurveyManager :: APPLICATION_NAME, $condition);
        
        $answers = array();
        
        while ($tracker = $trackers->next_result())
        {
            $answers[] = $tracker->get_answer();
        
        }
        
        $analyzer = SurveyAnalyzer :: factory($this->analyse_type, $this->question, $answers);
        
        return $analyzer->analyse();
    }

}

?>
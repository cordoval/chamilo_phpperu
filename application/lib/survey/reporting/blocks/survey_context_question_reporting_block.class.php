<?php
require_once dirname(__FILE__) . '/../survey_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../survey_manager/survey_manager.class.php';
require_once (dirname(__FILE__) . '/../../trackers/survey_question_answer_tracker.class.php');
require_once (dirname(__FILE__) . '/../../trackers/survey_participant_tracker.class.php');
require_once Path :: get_repository_path() . 'lib/content_object/survey/analyzer/analyzer.class.php';

class SurveyContextQuestionReportingBlock extends SurveyReportingBlock
{
    
    private $question_id;
    private $question;

    function SurveyContextQuestionReportingBlock($parent, $complex_question_id)
    {
        parent :: __construct($parent);
        $complex_question = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_question_id);
        $this->question = $complex_question->get_ref_object();
        $this->question_id = $this->question->get_id();
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
        

        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $this->question_id);
        
        $filter_parameters = $this->get_filter_parameters();
        
        $publication_id = $filter_parameters[SurveyReportingFilterWizard :: PARAM_PUBLICATION_ID];
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID, $publication_id);
        
        $context_ids = $filter_parameters[SurveyReportingFilterWizard :: PARAM_CONTEXTS];
              
        if (is_array($context_ids))
        {
            $conditions[] = new InCondition(SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID, $context_ids);
        }
        else
        {
            $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID, 0);
        }
                
        $condition = new AndCondition($conditions);
          
        $trackers = Tracker :: get_data(SurveyQuestionAnswerTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        
        $answers = array();
        
        while ($tracker = $trackers->next_result())
        {
            $answers[] = $tracker->get_answer();
        
        }
        
        $analyse_type = $filter_parameters[SurveyReportingFilterWizard :: PARAM_ANALYSE_TYPE];
        
        $analyzer = SurveyAnalyzer :: factory($analyse_type, $this->question, $answers);
        
        return $analyzer->analyse();
    }

}

?>
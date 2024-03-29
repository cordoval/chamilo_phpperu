<?php 
namespace application\survey;

use repository\RepositoryDataManager;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use tracking\Tracker;
use repository\content_object\survey\SurveyAnalyzer;

class SurveyQuestionReportingBlock extends SurveyReportingBlock
{
    
    private $publication_id;
	private $complex_question_id;
    private $question_id;
	    private $question;
    private $analyse_type;

    function __construct($parent, $complex_question_id, $publication_id , $analyse_type)
    {
        parent :: __construct($parent);
        $complex_question = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_question_id);
        $this->complex_question_id = $complex_question_id;
        $this->question = $complex_question->get_ref_object();
        $this->question_id = $this->question->get_id();
        $this->publication_id = $publication_id;
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
        return $this->count_data();
    }

    function get_application()
    {
        return SurveyManager :: APPLICATION_NAME;
    }

    private function create_reporting_data()
    {
        
        //retrieve the answer trackers
        

        $conditions = array();
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID, $this->complex_question_id);
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID, $this->publication_id);
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
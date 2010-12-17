<?php 
namespace application\survey;

use reporting\ReportingTemplate;
use common\libraries\Request;
use common\libraries\DynamicFormTabsRenderer;
use common\libraries\Translation;


class SurveyContextRelUserReportingTemplate extends ReportingTemplate implements SurveyLevelReportingTemplateInterface
{
    
    private $filter_parameters;
    //private $wizard;
    private $aggregated;

    function __construct($parent)
    {
        parent :: __construct($parent);
        
        $publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);

        $publication = SurveyDataManager::get_instance()->retrieve_survey_publication($publication_id);
        $survey = $publication->get_publication_object();
        
      	
        $complex_question_ids = array_keys($survey->get_complex_questions());
        
        foreach ($complex_question_ids as $complex_question_id)
        {
            $this->add_reporting_block(new SurveyQuestionReportingBlock($this, $complex_question_id, $publication_id, SurveyAnalyzer::TYPE_ABSOLUTE));
        }
            
    }

    public function display_context()
    {
    
    }

    function get_application()
    {
        return SurveyManager :: APPLICATION_NAME;
    }
   
}
?>
<?php 
namespace application\survey;

use reporting\ReportingTemplate;
use common\libraries\Request;
use common\libraries\DynamicFormTabsRenderer;
use common\libraries\Translation;
use repository\content_object\survey\SurveyAnalyzer;

require_once dirname(__FILE__) . '/../blocks/survey_context_rel_user_reporting_block.class.php';

class SurveyContextRelUserReportingTemplate extends ReportingTemplate implements SurveyLevelReportingTemplateInterface
{
    //private $aggregated;

    function __construct($parent)
    {
        parent :: __construct($parent);
        
        $publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);

        $publication = SurveyDataManager::get_instance()->retrieve_survey_publication($publication_id);
        $survey = $publication->get_publication_object();
        
      	
        $complex_question_ids = array_keys($survey->get_complex_questions());
        
        foreach ($complex_question_ids as $complex_question_id)
        {
            $this->add_reporting_block(new SurveyContextRelUserReportingBlock($this, $complex_question_id, $publication_id, SurveyAnalyzer::TYPE_ABSOLUTE));
         }
            
    }
    
    public function display_context()
    {
    	
    }
    
    public function get_user_id()
    {
   		return $this->parent->get_user_id();
    }
	
    function get_application()
    {
        return SurveyManager :: APPLICATION_NAME;
    }
   
}
?>
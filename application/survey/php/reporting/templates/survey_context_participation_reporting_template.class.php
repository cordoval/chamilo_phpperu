<?php
namespace application\survey;

use reporting\ReportingTemplate;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use repository\content_object\survey\SurveyContextDataManager;

class SurveyContextParticipationReportingTemplate extends ReportingTemplate implements SurveyLevelReportingTemplateInterface
{

    function __construct($parent)
    {
        parent :: __construct($parent);
        
        $publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        $context_template_id = Request :: get(SurveyReportingManager :: PARAM_CONTEXT_TEMPLATE_ID);
        if (isset($context_template_id))
        {
            $dm = SurveyContextDataManager :: get_instance();
            
            $context_template = $dm->retrieve_survey_context_template($context_template_id);
            $user_id = $parent->get_user_id();
            $this->add_reporting_block(new SurveyContextParticipantReportingBlock($this, $publication_id, $user_id, $context_template->get_context_type()));
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
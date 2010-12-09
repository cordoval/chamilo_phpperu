<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_total_result_view_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_total_user_result_view_reporting_block.class.php';

class SurveyResultViewReportingTemplate extends ReportingTemplate
{

    function SurveyResultViewReportingTemplate($parent)
    {
        parent :: __construct($parent);
        
        $publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);

        $this->add_reporting_block(new SurveyTotalResultViewReportingBlock($this, $publication_id, SurveyTotalResultViewReportingBlock :: ABSOLUTE));
        $this->add_reporting_block(new SurveyTotalResultViewReportingBlock($this, $publication_id, SurveyTotalResultViewReportingBlock :: PERCENTAGE));
        $this->add_reporting_block(new SurveyTotalUserResultViewReportingBlock($this, $publication_id, SurveyTotalUserResultViewReportingBlock :: VIEWED));
        $this->add_reporting_block(new SurveyTotalUserResultViewReportingBlock($this, $publication_id, SurveyTotalUserResultViewReportingBlock :: NOT_VIEWED));
    
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
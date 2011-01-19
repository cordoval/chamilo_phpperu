<?php
namespace application\survey;

use reporting\ReportingTemplate;
use common\libraries\Request;

class SurveyResultViewReportingTemplate extends ReportingTemplate
{

    function __construct($parent)
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
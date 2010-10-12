<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_context_reporting_filter_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_question_answer_reporting_filter_block.class.php';
require_once Path :: get_application_path() . 'lib/survey/wizards/survey_reporting_filter_wizard.class.php';

class SurveyQuestionAnswerReportingFilterTemplate extends ReportingTemplate
{
    private $filter_parameters;
    private $wizard;

    function SurveyQuestionAnswerReportingFilterTemplate($parent)
    {
        parent :: __construct($parent);
        
        $ids = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        Request :: set_get(DynamicFormTabsRenderer :: PARAM_SELECTED_TAB, Request :: post('submit'));
        $this->wizard = new SurveyReportingFilterWizard(array(SurveyReportingFilterWizard :: TYPE_CONTEXTS), $ids, $this->get_url($this->get_parameters()));
        $this->filter_parameters = $this->wizard->get_filter_parameters();
        $this->set_filter_parameters();
        
        $this->add_reporting_block(new SurveyContextReportingFilterBlock($this));
        $this->add_reporting_block(new SurveyQuestionAnswerReportingFilterBlock($this));
    }

    public function display_filter()
    {
        return $this->wizard->toHtml();
    }

    public function display_context()
    {
    
    }

    function get_application()
    {
        return SurveyManager :: APPLICATION_NAME;
    }

    function get_filter_parameters()
    {
        return $this->filter_parameters;
    }

    function set_filter_parameters()
    {
        $wizard = $this->wizard;
        $parameters = $wizard->get_filter_parameters();
        
        foreach ($parameters as $key => $parameter)
        {
            $this->get_parent()->set_parameter($key, $parameter);
        }
    }

}
?>
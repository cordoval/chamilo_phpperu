<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_context_question_reporting_block.class.php';
require_once Path :: get_application_path() . 'lib/survey/wizards/survey_reporting_filter_wizard.class.php';
require_once Path :: get_application_path() . 'lib/survey/reporting/survey_level_reporting_template_interface.class.php';

class SurveyContextQuestionReportingTemplate extends ReportingTemplate implements SurveyLevelReportingTemplateInterface
{
    
    private $filter_parameters;
    private $wizard;

    function SurveyContextQuestionReportingTemplate($parent)
    {
        parent :: __construct($parent);
        
        $publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
         
        Request :: set_get(DynamicFormTabsRenderer :: PARAM_SELECTED_TAB, Request :: post('submit'));
        $types = array(SurveyReportingFilterWizard :: TYPE_CONTEXTS, SurveyReportingFilterWizard :: TYPE_QUESTIONS, SurveyReportingFilterWizard :: TYPE_ANALYSE_TYPE);
        
        $this->wizard = new SurveyReportingFilterWizard($types, $publication_id, $this->get_url($this->get_parent()->get_parameters()), $this->get_parent()->get_user());
        
        $this->filter_parameters = $this->wizard->get_filter_parameters();
        $complex_question_ids = $this->filter_parameters[SurveyReportingFilterWizard :: PARAM_QUESTIONS];
        
        foreach ($complex_question_ids as $complex_question_id)
        {
            $this->add_reporting_block(new SurveyContextQuestionReportingBlock($this, $complex_question_id));
        }
        
        $this->set_filter_parameters();
     
    }

    public function display_filter_body()
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
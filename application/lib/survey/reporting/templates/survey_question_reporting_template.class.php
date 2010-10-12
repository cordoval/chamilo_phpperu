<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_question_reporting_block.class.php';
require_once Path :: get_application_path() . 'lib/survey/wizards/survey_reporting_filter_wizard.class.php';

class SurveyQuestionReportingTemplate extends ReportingTemplate
{
    
    private $filter_parameters;
    private $wizard;

    function SurveyQuestionReportingTemplate($parent)
    {
        parent :: __construct($parent);
        
        $ids = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
        }
        
        Request :: set_get(DynamicFormTabsRenderer :: PARAM_SELECTED_TAB, Request :: post('submit'));
        $types = array(SurveyReportingFilterWizard :: TYPE_QUESTIONS, SurveyReportingFilterWizard :: TYPE_GROUPS, SurveyReportingFilterWizard :: TYPE_CONTEXT_TEMPLATES );
        $this->wizard = new SurveyReportingFilterWizard($types , $ids, $this->get_url($this->get_parameters()));
       
        $this->filter_parameters = $this->wizard->get_filter_parameters();
        
        $question_ids = $this->filter_parameters[SurveyReportingFilterWizard :: PARAM_QUESTIONS];
         
        foreach ($question_ids as $question_id)
        {
            $this->add_reporting_block(new SurveyQuestionReportingBlock($this, $question_id));
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
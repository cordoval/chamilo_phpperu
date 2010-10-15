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
        
        $context_template_id = Request :: get(SurveyReportingManager ::PARAM_CONTEXT_TEMPLATE_ID);
               
        $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($publication_id);
        $survey = $publication->get_publication_object();
      
        $template_question_ids = array();
//        if (count($context_template_ids))
//        {
            $template_question_ids = array_keys($survey->get_complex_questions_for_context_template_ids(array($context_template_id)));
//        }
        //        dump($template_question_ids);
        //        
        //       exit;
        

        $complex_question_ids = $this->filter_parameters[SurveyReportingFilterWizard :: PARAM_QUESTIONS];
        
        if (count($complex_question_ids))
        {
            $template_question_ids = array_intersect($complex_question_ids, $template_question_ids);
        }
        else
        {
            $template_question_ids = array();
        }
        
        foreach ($template_question_ids as $template_question_id)
        {
            $this->add_reporting_block(new SurveyContextQuestionReportingBlock($this, $template_question_id));
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
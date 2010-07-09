<?php
require_once dirname(__FILE__) . '/../survey_publication.class.php';

class SurveyReportingFilterWizard extends WizardPageValidator
{
	function SurveyReportingFilterWizard($user,$selected_survey_ids, $actions)
    {
    	parent :: __construct('survey_reporting_filter', 'post', $actions);   
    	
    	$this->addElement('category',Translation :: get('AvailableContexts'));
    	
    	$select_box_items = array();

		foreach ($selected_survey_ids as $id)
        {   
        	$pub = SurveyDataManager::get_instance()->retrieve_survey_publication($id);
        	$survey = $pub->get_publication_object();
        	
            $context = $survey->get_context_template();
            $context_id = $context->get_id();
            $context_name = $context->get_name();
			
            $select_box_items[ $context_id ] = $context_name;
           	
        }
        $this->add_select('select','AvailableContexts',$select_box_items,false,Translation :: get('SelectContext'));
        
        $this->addElement('category'); 	
    }
    
}

?>
<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once Path::get_application_path().'lib/survey/wizards/survey_reporting_filter_wizard.class.php';

class SurveyManagerReportingFilterComponent extends SurveyManager
{
	private $wizard;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	
    	if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: VIEW_RIGHT, 'reporter', SurveyRights :: TYPE_SURVEY_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        else 
        {      	 
        	$this->wizard = new SurveyReportingFilterWizard($this->get_user(), $ids, $this->get_url($parameters));       	
        	$publication_ids = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        	
	        if (! empty($publication_ids))
	        {
	            if (! is_array($publication_ids))
	            {
	                $ids = array($publication_ids);
	            }
	            
	            if ($test_case === 1)
		        {
		            $testcase = true;
		        }
		        
		        $trail = BreadcrumbTrail :: get_instance();
		        if($testcase)
		        {
		        	//$trail->add(new Breadcrumb($this->get_testcase_url(), Translation :: get('BrowseTestCaseSurveyPublications')));
		        }
		        else
		        {
		        	//$trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
		        }
				//$trail->add(new Breadcrumb($this->get_reporting_filter_survey_publication_url(), Translation :: get('ReportingFilter')));
		        $trail->add_help('survey reporting filter'); 
		        
		        //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewResults')));
        
		        if ($this->wizard->validate())
		        {
		        	
		        }
		        else
		        {
		        	$classname = Request :: get(ReportingManager :: PARAM_TEMPLATE_NAME);
		
        			$rtv = new ReportingViewer($this);
        			
        			foreach ($publication_ids as $publication_id)
        			{
						$this->set_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION, $publication_id);
        				$rtv->add_template_by_name('survey_publication_reporting_template', SurveyManager :: APPLICATION_NAME);
			        	
        			}
        			$rtv->show_all_blocks();	
	                $rtv->run();
	                
        			$rtv2 = new ReportingViewer($this);
        			
        			$question_ids = Request :: get(SurveyManager :: PARAM_SURVEY_QUESTION);
					foreach($question_ids as $question_id)
        			{
        					$this->set_parameter(SurveyManager :: PARAM_SURVEY_QUESTION, $question_id);
        					$rtv2->add_template_by_name('survey_question_reporting_template', SurveyManager :: APPLICATION_NAME);
        			}
        			
        			$rtv2->show_all_blocks();	
	                $rtv2->run();
	               
	            }
	        
	        }
	        else
	        {
	            $this->display_error_page(htmlentities(Translation :: get('NoSurveyPublicationsSelected')));
	        }
        }
    }
    
	function display_header($trail)
    {
    	parent::display_header();
        
        $this->wizard->display();
    	
    }
  
}
?>
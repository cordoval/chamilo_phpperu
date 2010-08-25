<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once Path::get_application_path().'lib/survey/wizards/survey_reporting_filter_wizard.class.php';

class SurveyManagerReportingFilterComponent extends SurveyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	
    	if (! SurveyRights :: is_allowed(SurveyRights :: VIEW_RIGHT, 'reporter', SurveyRights :: TYPE_SURVEY_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        else 
        {      	        	
        	$ids = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        	
	        $testcase = false;
	        $test_case = Request :: get(SurveyManager :: PARAM_TESTCASE);
	        if ($test_case === 1)
	        {
	            $testcase = true;
	        }
	        
	        if (! empty($ids))
	        {
	            if (! is_array($ids))
	            {
	                $ids = array($ids);
	            }
	            
	            $trail = BreadcrumbTrail :: get_instance();
		        if($testcase)
		        {
		        	$trail->add(new Breadcrumb($this->get_testcase_url(), Translation :: get('BrowseTestCaseSurveyPublications')));
		        }
		        else
		        {
		        	$trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
		        }
				$trail->add(new Breadcrumb($this->get_reporting_filter_survey_publication_url(), Translation :: get('ReportingFilter')));
		        $trail->add_help('survey reporting filter');
	            
		        $wizard = new SurveyReportingFilterWizard($this->get_user(), $ids, $this->get_url($parameters));
		        
	            $this->display_header($trail, true);
	            echo $wizard->toHtml();
	            /*
	             Just a little pretest
	             if(Request :: get("filter") != null)
	            {
	            	
	            	$reporting_viewer = new ReportingViewer($this);
	            	echo $reporting_viewer->show_all_blocks();
	            }*/
	            $this->display_footer();
	        
	        }
	        else
	        {
	            $this->display_error_page(htmlentities(Translation :: get('NoSurveyPublicationsSelected')));
	        }
        }
    }
}
?>
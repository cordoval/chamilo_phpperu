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
    	
    	 if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_VIEW, SurveyRights :: LOCATION_REPORTING, SurveyRights :: TYPE_SURVEY_COMPONENT))
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
	                $publication_ids = array($publication_ids);
	            }
		        
		        $trail = BreadcrumbTrail :: get_instance();
		       
		       $trail->add_help('survey reporting filter'); 
		        
	
        
		        if ($this->wizard->validate())
		        {
		        	
		        }
		        else
		        {
		        	
		        	$rtv = ReportingViewer :: construct($this);
        			
        			foreach ($publication_ids as $publication_id)
        			{
						$this->set_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION, $publication_id);
									        	
        			}
        			$rtv->add_template_by_name('survey_publication_reporting_filter_template', SurveyManager :: APPLICATION_NAME);
        			$rtv->set_breadcrumb_trail($trail);
        			$rtv->hide_all_blocks();
        			$rtv->run();           
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
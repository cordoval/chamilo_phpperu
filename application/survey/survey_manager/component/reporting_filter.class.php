<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';

class SurveyManagerReportingFilterComponent extends SurveyManager
{

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
            $publication_ids = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
            
            if (! empty($publication_ids))
            {
                if (! is_array($publication_ids))
                {
                    $publication_ids = array($publication_ids);
                }
                
                $trail = BreadcrumbTrail :: get_instance();
                
                $trail->add_help('survey reporting filter');
                                 
                $rtv = ReportingViewer :: construct($this);
     
                foreach ($publication_ids as $publication_id)
                {
                    $this->set_parameter(SurveyManager :: PARAM_PUBLICATION_ID, $publication_id);   
                }
                $rtv->add_template_by_name('survey_publication_reporting_filter_template', SurveyManager :: APPLICATION_NAME);
                $rtv->set_breadcrumb_trail($trail);
                $rtv->hide_all_blocks();
                $rtv->run();
            }
            else
            {
                $this->display_error_page(htmlentities(Translation :: get('NoSurveyPublicationsSelected')));
            }
        }
    }

    function display_header($trail)
    {
        parent :: display_header();   
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }
}
?>
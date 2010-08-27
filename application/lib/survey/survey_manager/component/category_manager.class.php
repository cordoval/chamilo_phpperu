<?php

require_once dirname(__FILE__) . '/../survey_manager.class.php';

require_once dirname(__FILE__) . '/../../category_manager/survey_publication_category_manager.class.php';

class SurveyManagerCategoryManagerComponent extends SurveyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
    	if (! SurveyRights :: is_allowed(SurveyRights :: VIEW_RIGHT, 'category_manager', SurveyRights :: TYPE_SURVEY_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
    	
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        $trail->add(new Breadcrumb($this->get_manage_survey_publication_categories_url(), Translation :: get('ManageCategories')));
        $category_manager = new SurveyPublicationCategoryManager($this, $trail);
        $category_manager->run();
    }
}
?>
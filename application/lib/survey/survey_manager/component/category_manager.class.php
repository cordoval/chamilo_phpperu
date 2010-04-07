<?php

require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/../survey_manager_component.class.php';
require_once dirname(__FILE__) . '/../../category_manager/survey_publication_category_manager.class.php';

class SurveyManagerCategoryManagerComponent extends SurveyManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_browse_survey_publications_url(), Translation :: get('BrowseSurveyPublications')));
        $trail->add(new Breadcrumb($this->get_manage_survey_publication_categories_url(), Translation :: get('ManageCategories')));
        $category_manager = new SurveyPublicationCategoryManager($this, $trail);
        $category_manager->run();
    }
}
?>
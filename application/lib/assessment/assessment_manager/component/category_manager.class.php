<?php
/**
 * $Id: category_manager.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../category_manager/assessment_publication_category_manager.class.php';

/**
 * Component to manage assessment publication categories
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerCategoryManagerComponent extends AssessmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageCategories')));
        $category_manager = new AssessmentPublicationCategoryManager($this, $trail);
        $category_manager->run();
    }
}
?>
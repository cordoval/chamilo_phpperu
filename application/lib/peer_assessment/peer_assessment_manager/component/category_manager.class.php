<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../category_manager/peer_assessment_publication_category_manager.class.php';
/**
 *	@author Nick Van Loocke
 */

class PeerAssessmentManagerCategoryManagerComponent extends PeerAssessmentManager
{
    private $action_bar;

	function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('PeerAssessment')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageCategories')));

    	if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        $category_manager = new PeerAssessmentPublicationCategoryManager($this, $trail);
        $category_manager->run();   
    }
}
?>
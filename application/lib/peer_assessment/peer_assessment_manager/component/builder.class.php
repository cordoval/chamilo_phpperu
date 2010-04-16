<?php 
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/../../peer_assessment_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/peer_assessment_publication_browser/peer_assessment_publication_browser_table.class.php';

class PeerAssessmentManagerBuilderComponent extends PeerAssessmentManagerComponent
{
    function run()
    {
    	$publication_id = Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION);
    	$publication = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication($publication_id);
    	$this->set_parameter(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION, $publication_id);
    	Request :: set_get(ComplexBuilder :: PARAM_ROOT_LO, $publication->get_content_object());
    	
    	$complex_builder = ComplexBuilder :: factory($this);
    	$complex_builder->run();
    }
    
    function display_header($trail)
    {
    	$new_trail = new BreadcrumbTrail();
    	$new_trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowsePeerAssessmentPublications')));
    	
    	$new_trail->merge($trail);
    	parent :: display_header($new_trail);
    }
}
?>
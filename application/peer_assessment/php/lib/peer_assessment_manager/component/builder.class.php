<?php 
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../peer_assessment_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/peer_assessment_publication_browser/peer_assessment_publication_browser_table.class.php';

class PeerAssessmentManagerBuilderComponent extends PeerAssessmentManager
{
	private $content_object;
	
    function run()
    {
    	$publication_id = Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION);
    	$publication = PeerAssessmentDataManager :: get_instance()->retrieve_peer_assessment_publication($publication_id);
    	$this->set_parameter(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION, $publication_id);
    	$this->content_object = $publication->get_content_object();
    	
        ComplexBuilder :: launch($this->content_object->get_type(), $this);
        //$complex_builder = ComplexBuilder :: factory($this, $this->content_object->get_type());
        //$complex_builder->run();
    }
    
    function display_header($trail)
    {
    	$new_trail = BreadcrumbTrail :: get_instance();
    	$new_trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowsePeerAssessmentPublications')));
    	
    	if($trail)
    	{
    		$new_trail->merge($trail);
    	}
    	parent :: display_header($new_trail, true);
    }
    
    function get_root_content_object()
    {
    	return $this->content_object;
    }
}
?>
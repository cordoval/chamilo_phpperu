<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/../../publisher/peer_assessment_publication_publisher.class.php';
require_once dirname(__FILE__) . '/../../forms/peer_assessment_publication_form.class.php';
/**
 * @author Nick Van Loocke
 */

class PeerAssessmentManagerCreatorComponent extends PeerAssessmentManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE)), Translation :: get('BrowsePeerAssessment')));
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE)), Translation :: get('BrowsePeerAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishPeerAssessment')));
        
        $object = Request :: get('object');
        $pub = new RepoViewer($this, 'peer_assessment', true);
        
        if (! isset($object))
        {
            
            $html[] = $pub->as_html();
        }
        else
        {
            $publisher = new PeerAssessmentPublicationPublisher($pub);
            $html[] = $publisher->publish($object);
        }
        
        $this->display_header($trail);
        
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>
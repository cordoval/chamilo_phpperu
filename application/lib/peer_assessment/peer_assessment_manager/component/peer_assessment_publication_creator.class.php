<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/peer_assessment_publication_form.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/content_object_repo_viewer.class.php';

/**
 * Component to create a new peer_assessment_publication object
 * @author Sven Vanpoucke & Stefan Billiet
 */
class PeerAssessmentManagerPeerAssessmentPublicationCreatorComponent extends PeerAssessmentManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('PeerAssessment')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishPeerAssessment')));
        
        $objects = Request :: get('object');
        
        /*
         *  We make use of the ContentObjectRepoViewer setting the type to peer_assessment
         */
        $pub = new RepoViewer($this, 'peer_assessment', true);
        
        /*
         *  If no page was created you'll be redirected to the peer_assessment_browser page, otherwise we'll get publications from the object
         */
        $this->display_header($trail, true);
        
        if (empty($objects))
        {
            echo $pub->as_html();
        }
        else
        {
            $form = new PeerAssessmentPublicationForm(PeerAssessmentPublicationForm :: TYPE_CREATE, null, $this->get_url(array('object' => $objects)), $this->get_user());
            if ($form->validate())
            {
                $values = $form->exportValues();
            	$failures = 0;
            	
            	if(!is_array($objects))
            		$objects = array($objects);
            	
                foreach($objects as $object)
                {
                	if(!$form->create_peer_assessment_publication($object, $values))
                		$failures++;
                }
                $message = $this->get_result($failures, count($objects), 'PeerAssessmentPublicationNotCreated', 'PeerAssessmentPublicationsNotCreated', 'PeerAssessmentPublicationCreated', 'PeerAssessmentPublicationsCreated');               
                $this->redirect($message, $failures, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
            }
            else
            {
                $form->display();
            }
        }
        
        //		echo implode("\n",$html);
        $this->display_footer();
    }
}
?>
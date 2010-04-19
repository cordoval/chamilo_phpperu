<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/peer_assessment_publication_form.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/content_object_repo_viewer.class.php';

/**
 * Component to create a new peer_assessment_publication object
 * @author Nick Van Loocke
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
        
        /*
         *  We make use of the ContentObjectRepoViewer setting the type to peer_assessment
         */
        $pub = new RepoViewer($this, PeerAssessment :: get_type_name());
        
        /*
         *  If no page was created you'll be redirected to the peer_assessment_browser page, otherwise we'll get publications from the object
         */
        $this->display_header($trail, true);
        
        if (!$pub->is_ready_to_be_published())
        {
            echo $pub->as_html();
        }
        else
        {
            $form = new PeerAssessmentPublicationForm(PeerAssessmentPublicationForm :: TYPE_CREATE, null, $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $pub->get_selected_objects())), $this->get_user());
            if ($form->validate())
            {
                $values = $form->exportValues();
            	$failures = 0;
            	
            	$objects = $pub->get_selected_objects();
            	
            	if(!is_array($objects))
            	{
            		$objects = array($objects);
            	}
            	
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
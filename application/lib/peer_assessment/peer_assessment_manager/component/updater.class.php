<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/peer_assessment_publication_form.class.php';
require_once dirname(__FILE__) . '/../../publisher/peer_assessment_publication_publisher.class.php';

/**
 * Component to edit an existing peer_assessment_publication object
 * @author Nick Van Loocke
 */
class PeerAssessmentManagerUpdaterComponent extends PeerAssessmentManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowsePeerAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdatePeerAssessmentPublication')));
        
        $publication = Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION);
        
        if (isset($publication))
        {
            $peer_assessment_publication = $this->retrieve_peer_assessment_publication($publication);
            
            if (! $peer_assessment_publication->is_visible_for_target_user($this->get_user()))
            {
                $this->not_allowed($trail, false);
            }

	        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $peer_assessment_publication->get_content_object(), 'edit', 'post', $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id())));
	        $this->display_header($trail);

			if ($form->validate() /*|| Request :: get('validated')*/)
	        {
	            //if (! Request :: get('validated'))
	              //  $success = $form->update_content_object();
	            
	            $publication_form = new PeerAssessmentPublicationForm(PeerAssessmentPublicationForm :: TYPE_EDIT, $peer_assessment_publication, $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id(), 'validated' => 1)), $this->get_user());
	            $publication_form->set_publication_values($peer_assessment_publication);

	            if ($publication_form->validate())
	            {
	                $success = $publication_form->update_content_object();
	                $category_id = $peer_assessment_publication->get_category();
	                //$this->redirect($message, null, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));       
	                $this->redirect($success ? Translation :: get('PeerAssessmentPublicationUpdated') : Translation :: get('PeerAssessmentPublicationNotUpdated'), ! $success, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS, 'category' => $category_id));
	            }
	            else
	            {
	            	// Probably could be done cleaner           	
	            	$publisher = new PeerAssessmentPublicationPublisher($publication_form); 
	            	$id = $peer_assessment_publication->get_content_object()->get_id();
	            	echo $publisher->get_content_object_title($id); 
	                $publication_form->display();
	            }      
	        }
	        else
	        {
	            $form->display();
	        }
        
        	$this->display_footer();
        }
    }
}
?>
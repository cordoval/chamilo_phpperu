<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/peer_assessment_publication_form.class.php';
require_once dirname(__FILE__) . '/../../publisher/peer_assessment_publication_publisher.class.php';

/**
 * Component to edit an existing peer_assessment_publication object
 * @author Nick Van Loocke
 */
class PeerAssessmentManagerUpdaterComponent extends PeerAssessmentManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowsePeerAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_EDIT_PEER_ASSESSMENT_PUBLICATION)) . '&peer_assessment_publication=' . Request :: get('peer_assessment_publication'), Translation :: get('UpdatePeerAssessmentPublication')));
        
        $publication = Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION);
        
        if (isset($publication))
        {
            $peer_assessment_publication = $this->retrieve_peer_assessment_publication($publication);
            
            if (! $peer_assessment_publication->is_visible_for_target_user($this->get_user()))
            {
                $this->not_allowed($trail, false);
            }

	   		// Form with title, description and new version option
            $form_main = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $peer_assessment_publication->get_content_object(), 'edit', 'post', $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id())));
			// Form with publish for, from date, to date and hidden option
            $form_properties = new PeerAssessmentPublicationForm(PeerAssessmentPublicationForm :: TYPE_EDIT, $peer_assessment_publication, $this->get_user(), $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id(), 'validated' => 1)));
	        $form_properties->set_publication_values($peer_assessment_publication);
	        
			if ($form_main->validate())
	        {
	        	// Update the title, description and new version option
	        	$form_main->update_content_object();       
	            
	            if (!$form_properties->validate())
	            {	
	            	$this->display_header($trail, true);
	            	          	
	            	$publisher = new PeerAssessmentPublicationPublisher($form_properties); 
	            	$id = $peer_assessment_publication->get_content_object()->get_id();
	            	echo $publisher->get_content_object_title($id); 
	            	
	                $form_properties->display();
	            } 
	        }
	        else
	        {
	            if ($form_properties->validate())
	            {
	            	// Update the publish form, from date, to date and hidden option
	            	$html[] = $form_properties->update_content_object();
	                $category_id = $peer_assessment_publication->get_category();
	                
	                $selected_button_value = $form_properties->getSubmitValue('buttons');
	                foreach($selected_button_value as $selected_button)
	                {
	                	if($selected_button == 'Update....')
	                	{
	                		$html[] = $this->redirect($html[0] ? Translation :: get('PeerAssessmentPublicationUpdated') : Translation :: get('PeerAssessmentPublicationNotUpdated'), ! $html[0], array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS, 'category' => $category_id));
	                	}
	                	else
	                	{
	                		$html[] = $this->redirect($html[0] ? Translation :: get('PeerAssessmentPublicationUpdated') : Translation :: get('PeerAssessmentPublicationNotUpdated'), ! $html[0], array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BUILD_PEER_ASSESSMENT_PUBLICATION, 'peer_assessment_publication' => $peer_assessment_publication->get_id()));
	                	}
	                }
	            }
	            else
	            {
	            	$this->display_header($trail, true);
	       			$form_main->display();
	            }
			}
        
	        echo implode("\n", $html);
        	echo '<div style="clear: both;"></div>';
        	$this->display_footer();
        }
    }
}
?>
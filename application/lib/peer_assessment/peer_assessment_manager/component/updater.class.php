<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/peer_assessment_publication_form.class.php';

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
        /*$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('PeerAssessment')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdatePeerAssessmentPublication')));
        
        $peer_assessment_publication = $this->retrieve_peer_assessment_publication(Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION));
        
        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $peer_assessment_publication->get_content_object(), 'edit', 'post', $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id())));
        $this->display_header($trail);
        
		if ($form->validate() || Request :: get('validated'))
        {
            if (! Request :: get('validated'))
                $success = $form->update_content_object();
            
            pub_form = new PeerAssessmentPublicationForm(PeerAssessmentPublicationForm :: TYPE_EDIT, $peer_assessment_publication, $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id(), 'validated' => 1)), $this->get_user());
            if ($pub_form->validate())
            {
                $success = $pub_form->update_content_object();
                $category_id = $peer_assessment_publication->get_category();
                //$this->redirect($message, null, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));       

                //$this->redirect($success ? Translation :: get('PeerAssessmentPublicationUpdated') : Translation :: get('PeerAssessmentPublicationNotUpdated'), ! $success, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS, 'category' => $category_id));
            }
            else
            {
                $pub_form->display();
            }      
        }
        else
        {
            $form->display();
        }
        
        $this->display_footer();*/
    }
}
?>
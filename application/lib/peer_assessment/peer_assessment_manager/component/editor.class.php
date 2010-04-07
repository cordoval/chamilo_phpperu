<?php
/**
 *	@author Nick Van Loocke
 */

class PeerAssessmentManagerEditorComponent extends PeerAssessmentManagerComponent
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $pid = Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION);
            
            $datamanager = PeerAssessmentDataManager :: get_instance();
            $publication = $datamanager->retrieve_peer_assessment_publication($pid);
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($publication->get_peer_assessment_id());
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_EDIT, PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pid)));
            
            $trail = new BreadcrumbTrail();
            
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_EDIT, PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pid)), Translation :: get('Edit')));
            $trail->add_help('peer_assessment general');
            
            if ($form->validate())
            {
                $succes = $form->update_content_object();
                
                if ($form->is_version())
                {
                    $old_id = $publication->get_peer_assessment_id();
                    $publication->set_peer_assessment_id($content_object->get_latest_version()->get_id());
                    $publication->update();
                    
                    RepositoryDataManager :: get_instance()->set_new_clo_version($old_id, $publication->get_peer_assessment_id());
                }
                
                $message = $succes ? Translation :: get('PeerAssessmentUpdated') : Translation :: get('PeerAssessmentNotUpdated');
                $this->redirect($message, ! $succes, array(PeerAssessmentManager :: PARAM_ACTION => null));
            }
            else
            {
                $this->display_header($trail, true);
                $form->display();
                $this->display_footer();
            }
        }
    }
}
?>
<?php
require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/peer_assessment_publication_form.class.php';
require_once dirname(__FILE__) . '/../../publisher/peer_assessment_publication_publisher.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/content_object_repo_viewer.class.php';

/**
 * Component to create a new peer_assessment_publication object
 * @author Nick Van Loocke
 */
class PeerAssessmentManagerCreatorComponent extends PeerAssessmentManager implements RepoViewerInterface
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('PeerAssessment')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishPeerAssessment')));

        
        $form = new PeerAssessmentPublicationForm(PeerAssessmentPublicationForm :: TYPE_CREATE, null, $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER)), $this->get_user());

        if (!RepoViewer :: is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new PeerAssessmentPublicationPublisher($this);

            if ($form->validate())
            {
                $content_object_id = RepoViewer :: get_selected_objects();
                $published = $publisher->publish_content_object($content_object_id);

                if (! $published)
                {
                    $message = Translation :: get('ObjectNotPublished');
                }
                else
                {
                    $message = Translation :: get('ObjectPublished');
                    $peer_assessment_publication = $this->retrieve_peer_assessment_publication_via_content_object($content_object_id);
                }

                $selected_button_value = $form->getSubmitValue('buttons');
                foreach ($selected_button_value as $selected_button)
                {
                    if ($selected_button == 'Publish')
                    {
                        $this->redirect($message, null, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
                    }
                    else
                    {
                        $this->redirect($message, null, array(
                                PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BUILD_PEER_ASSESSMENT_PUBLICATION,
                                'peer_assessment_publication' => $peer_assessment_publication->get_id()));
                    }
                }
            }
            else
            {
                $this->display_header($trail, true);
                echo $publisher->get_content_object_title(RepoViewer :: get_selected_objects());
                $form->display();
                $this->display_footer();
            }
        }
    }

    function get_allowed_content_object_types()
    {
        return array(PeerAssessment :: get_type_name());
    }
}
?>
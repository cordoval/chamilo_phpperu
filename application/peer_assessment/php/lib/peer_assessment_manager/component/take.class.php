<?php

namespace application\peer_assessment;

use common\libraries\Request;
use repository\RepositoryDataManager;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use repository\ComplexDisplay;
use repository\content_object\peer_assessment\PeerAssessment;
use common\libraries\FormValidator;

require_once dirname(__FILE__) . '/../peer_assessment_manager.class.php';

/**
 * Component to complete a peer assessment
 * @author Nick Van Loocke
 */
class PeerAssessmentManagerTakeComponent extends PeerAssessmentManager
{

    private $datamanager;
    private $peer_assessment;
    private $pid;
    private $form;

    function run()
    {
        $pid = Request :: get(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION);
        if (!$pid || (is_array($pid) && count($pid) == 0))
        {
            $this->not_allowed();
            exit();
        }
        $pids = $pid;

        if (is_array($pids))
        {
            $pid = $pids[0];
        }

        $publication = $this->retrieve_peer_assessment_publication($pid);

        if (!$publication->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed(null, false);
        }

        $this->datamanager = PeerAssessmentDataManager :: get_instance();

        $this->pid = $pid;
        $this->pub = $this->datamanager->retrieve_peer_assessment_publication($this->pid);
        $peer_assessment_id = $publication->get_content_object()->get_object_number();
        $this->peer_assessment = RepositoryDataManager :: get_instance()->retrieve_content_object($peer_assessment_id);
        $this->set_parameter(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION, $this->pid);

        $this->form = $this->build_result_form($pids);
        if ($this->form->validate())
        {
            $this->redirect(Translation :: get('PeerAssessmentChecked'), false, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowsePeerAssessmentPublications')));
            $trail->add(new Breadcrumb($this->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_TAKE_PEER_ASSESSMENT_PUBLICATION, PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pid)), Translation :: get('TakePeerAssessment')));

            ComplexDisplay :: launch(PeerAssessment :: get_type_name(), $this);
        }
    }

    function display_footer()
    {
        if (!$this->form->validate())
        {
            $this->form->toHtml();
        }

        parent :: display_footer();
    }

    function build_result_form($pids)
    {
        $url = $this->get_url(array(PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $pids));
        $form = new FormValidator('take_peer_assessment_publication', 'post', $url);

        return $form;
    }

    function get_current_attempt_id()
    {
        return $this->peer_assessment->get_id();
    }

    function get_root_content_object()
    {
        return $this->peer_assessment;
    }

}

?>
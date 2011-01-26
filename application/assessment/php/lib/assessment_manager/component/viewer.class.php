<?php
namespace application\assessment;

use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;

use tracking\Tracker;
use tracking\Event;

use repository\content_object\hotpotatoes\Hotpotatoes;

use repository\RepositoryDataManager;
use repository\ComplexDisplay;

use repository\content_object\assessment\AssessmentComplexDisplaySupport;
use repository\content_object\assessment\FeedbackDisplayConfiguration;

/**
 * $Id: viewer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */

require_once Path :: get_application_path() . '/assessment/php/trackers/assessment_assessment_attempts_tracker.class.php';
require_once Path :: get_application_path() . '/assessment/php/trackers/assessment_question_attempts_tracker.class.php';

class AssessmentManagerViewerComponent extends AssessmentManager implements AssessmentComplexDisplaySupport
{
    private $datamanager;

    private $pub;
    private $assessment;
    private $pid;
    private $active_tracker;
    private $trail;

    function run()
    {
        // Retrieving assessment
        $this->datamanager = AssessmentDataManager :: get_instance();
        if (Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION))
        {
            $this->pid = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
            $this->pub = $this->datamanager->retrieve_assessment_publication($this->pid);
            $assessment_id = $this->pub->get_content_object();
            $this->assessment = RepositoryDataManager :: get_instance()->retrieve_content_object($assessment_id);
        }

        if ($this->pub && ! $this->pub->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed(null, false);
        }

        // Checking statistics
        $conditions[] = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $this->pid);
        $conditions[] = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);

        $trackers = Tracker :: get_data(AssessmentAssessmentAttemptsTracker :: CLASS_NAME, AssessmentManager :: APPLICATION_NAME, $condition);
        $count = $trackers->size();

        while ($tracker = $trackers->next_result())
        {
            if ($tracker->get_status() == 'not attempted')
            {
                $this->active_tracker = $tracker;
                $count --;
                break;
            }
        }

        if ($this->assessment->get_maximum_attempts() != 0 && $count >= $this->assessment->get_maximum_attempts())
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('YouHaveReachedYourMaximumAttempts'));
            $this->display_footer();
            return;
        }

        if (! $this->active_tracker)
        {
            $this->active_tracker = $this->create_tracker();
        }

        // Executing assessment
        if ($this->assessment->get_assessment_type() == Hotpotatoes :: TYPE_HOTPOTATOES)
        {
            $this->display_header();

            $path = $this->assessment->add_javascript(Path :: get(WEB_PATH) . 'application/assessment/php/ajax/hotpotatoes_save_score.php', $this->get_browse_assessment_publications_url(), $this->active_tracker->get_id());
            echo '<iframe src="' . $path . '" width="100%" height="600">
  				 <p>Your browser does not support iframes.</p>
				 </iframe>';
            //require_once $path;
            $this->display_footer();
            exit();
        }
        else
        {
            ComplexDisplay :: launch($this->assessment->get_type(), $this);
        }

    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('assessment_viewer');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('AssessmentManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ASSESSMENT_PUBLICATION);
    }

    function get_root_content_object()
    {
        return $this->assessment;
    }

    function display_header($trail)
    {
        if ($trail)
        {
            $this->trail->merge($trail);
        }

        parent :: display_header($this->trail);
    }

    function create_tracker()
    {
        $parameters = array(AssessmentAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID => $this->pid,
                AssessmentAssessmentAttemptsTracker :: PROPERTY_USER_ID => $this->get_user_id(),
                AssessmentAssessmentAttemptsTracker :: PROPERTY_TOTAL_SCORE => 0);
        $tracker = Event :: trigger('attempt_assessment', AssessmentManager :: APPLICATION_NAME, $parameters);
        return $tracker[0];
    }

    function save_assessment_answer($complex_question_id, $answer, $score)
    {
        $parameters = array();
        $parameters[AssessmentQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID] = $this->active_tracker->get_id();
        $parameters[AssessmentQuestionAttemptsTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
        $parameters[AssessmentQuestionAttemptsTracker :: PROPERTY_ANSWER] = $answer;
        $parameters[AssessmentQuestionAttemptsTracker :: PROPERTY_SCORE] = $score;
        $parameters[AssessmentQuestionAttemptsTracker :: PROPERTY_FEEDBACK] = '';

        Event :: trigger('attempt_question', AssessmentManager :: APPLICATION_NAME, $parameters);
    }

    function save_assessment_result($total_score)
    {
        $tracker = $this->active_tracker;

        $tracker->set_total_score($total_score);
        $tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
        $tracker->set_status('completed');
        $tracker->update();
    }

    function get_assessment_current_attempt_id()
    {
        return $this->active_tracker->get_id();
    }

    function get_assessment_question_attempts()
    {
        $assessment_question_attempt_data = array();

        $condition = new EqualityCondition(AssessmentQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $this->active_tracker->get_id());

        $dummy = new AssessmentQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        foreach ($trackers as $tracker)
        {
            $assessment_question_attempt_data[$tracker->get_question_cid()] = $tracker;
        }

        return $assessment_question_attempt_data;
    }

    function get_assessment_question_attempt($complex_question_id)
    {
        $answers = $this->get_assessment_question_attempts($complex_question_id);
        return $answers[$complex_question_id];
    }

    function get_assessment_go_back_url()
    {
        return $this->get_url(array(
                AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS,
                AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => null));
    }

    function get_assessment_feedback_configuration()
    {
        $default_configuration = new FeedbackDisplayConfiguration();
        $default_configuration->set_feedback_type(FeedbackDisplayConfiguration :: TYPE_BOTH);
        $default_configuration->disable_feedback_per_page();
        $default_configuration->enable_feedback_summary();
        return $default_configuration;
    }

    /**
     * Unused for assessments
     */
    function is_allowed($right)
    {
        return true;
    }

    function get_assessment_continue_url()
    {

    }

    function get_assessment_back_url()
    {

    }
}
?>
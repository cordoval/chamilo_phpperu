<?php
namespace application\weblcms\tool\assessment;

use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\DelegateComponent;

use tracking\Event;

use repository\ComplexDisplay;

use repository\content_object\hotpotatoes\Hotpotatoes;
use repository\content_object\assessment\AssessmentComplexDisplaySupport;
use repository\content_object\assessment\FeedbackDisplayConfiguration;

use application\weblcms\WeblcmsDataManager;
use application\weblcms\Tool;
use application\weblcms\WeblcmsQuestionAttemptsTracker;
use application\weblcms\WeblcmsAssessmentAttemptsTracker;

/**
 * $Id: assessment_tester.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__) . '/../survey_invitation.class.php';
require_once Path :: get_application_path() . '/weblcms/php/trackers/weblcms_assessment_attempts_tracker.class.php';
require_once Path :: get_application_path() . '/weblcms/php/trackers/weblcms_question_attempts_tracker.class.php';

class AssessmentToolComplexDisplayComponent extends AssessmentTool implements
        AssessmentComplexDisplaySupport,
        DelegateComponent
{

    private $datamanager;
    private $pub;
    private $invitation;
    private $assessment;
    private $iid;
    private $publication_id;
    private $active_tracker;
    private $trail;

    function run()
    {
        // Retrieving assessment
        $this->datamanager = WeblcmsDataManager :: get_instance();

        if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $this->publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
            $this->pub = $this->datamanager->retrieve_content_object_publication($this->publication_id);
            $this->assessment = $this->pub->get_content_object();
            $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $this->publication_id);
        }

        //        if (Request :: get(AssessmentTool :: PARAM_INVITATION_ID))
        //        {
        //            $this->iid = Request :: get(AssessmentTool :: PARAM_INVITATION_ID);
        //            $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_INVITATION_CODE, $this->iid);
        //            $this->invitation = $this->datamanager->retrieve_survey_invitations($condition)->next_result();
        //            $this->pub = $this->datamanager->retrieve_content_object_publication($this->invitation->get_survey_id());
        //            $this->publication_id = $this->pub->get_id();
        //            $this->assessment = $this->pub->get_content_object();
        //            $this->set_parameter(AssessmentTool :: PARAM_INVITATION_ID, $this->iid);
        //        }
        // Checking statistics


        $track = new WeblcmsAssessmentAttemptsTracker();
        $conditions[] = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $this->publication_id);
        $conditions[] = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);
        $trackers = $track->retrieve_tracker_items($condition);

        $count = count($trackers);

        foreach ($trackers as $tracker)
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

            $path = $this->assessment->add_javascript(Path :: get(WEB_PATH) . 'application/weblcms/php/ajax/hotpotatoes_save_score.php', $this->get_assessment_go_back_url(), $this->active_tracker->get_id());
            //$path = $this->assessment->get_test_path();
            echo '<iframe src="' . $path . '" width="100%" height="600">
  				 <p>Your browser does not support iframes.</p>
				 </iframe>';
            //require_once $path;
            $this->display_footer();
            exit();
        }
        else
        {
            $this->trail = BreadcrumbTrail :: get_instance();
            //$this->trail->add(new Breadcrumb($this->get_url(array()), Translation :: get('TakeAssessment')));


            ComplexDisplay :: launch($this->assessment->get_type(), $this);
        }
    }

    function get_root_content_object()
    {
        return $this->assessment;
    }

    function display_header($trail)
    {
        return parent :: display_header();
    }

    function create_tracker()
    {
        $arguments = array(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID => $this->publication_id,
                WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID => $this->get_user_id(),
                WeblcmsAssessmentAttemptsTracker :: PROPERTY_COURSE_ID => $this->get_course_id(),
                WeblcmsAssessmentAttemptsTracker :: PROPERTY_TOTAL_SCORE => 0);
        $tracker = Event :: trigger('attempt_assessment', 'weblcms', $arguments);
        return $tracker[0];
    }

    function save_assessment_answer($complex_question_id, $answer, $score)
    {
        $parameters = array();
        $parameters[WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID] = $this->active_tracker->get_id();
        $parameters[WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
        $parameters[WeblcmsQuestionAttemptsTracker :: PROPERTY_ANSWER] = $answer;
        $parameters[WeblcmsQuestionAttemptsTracker :: PROPERTY_SCORE] = $score;
        $parameters[WeblcmsQuestionAttemptsTracker :: PROPERTY_FEEDBACK] = '';

        Event :: trigger('attempt_question', 'weblcms', $parameters);
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

        $condition = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $this->active_tracker->get_id());

        $dummy = new WeblcmsQuestionAttemptsTracker();
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
        return $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW));
    }

    function get_assessment_feedback_configuration()
    {
        $default_configuration = new FeedbackDisplayConfiguration();
        $default_configuration->set_feedback_type(FeedbackDisplayConfiguration :: TYPE_BOTH);
        $default_configuration->disable_feedback_per_page();
        $default_configuration->enable_feedback_summary();
        return $default_configuration;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('AssessmentToolBrowserComponent')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW,
                Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('AssessmentToolViewerComponent')));
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }

}

?>
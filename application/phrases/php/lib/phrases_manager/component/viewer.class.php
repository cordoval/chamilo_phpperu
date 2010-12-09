<?php
namespace application\phrases;

use common\libraries\Request;
use repository\RepositoryDataManager;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use tracking\Tracker;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use tracking\Event;
use repository\content_object\hotpotatoes\Hotpotatoes;
use repository\ComplexDisplay;
use repository\content_object\phrases\PhrasesComplexDisplaySupport;

/**
 * $Id: viewer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component
 */

require_once Path :: get_application_path() . '/phrases/php/trackers/phrases_phrases_attempts_tracker.class.php';
require_once Path :: get_application_path() . '/phrases/php/trackers/phrases_question_attempts_tracker.class.php';

class PhrasesManagerViewerComponent extends PhrasesManager implements PhrasesComplexDisplaySupport
{
    private $datamanager;

    private $pub;
    private $phrases;
    private $pid;
    private $active_tracker;
    private $trail;

    function run()
    {
        // Retrieving phrases
        $this->datamanager = PhrasesDataManager :: get_instance();
        if (Request :: get(PhrasesManager :: PARAM_PHRASES_PUBLICATION))
        {
            $this->pid = Request :: get(PhrasesManager :: PARAM_PHRASES_PUBLICATION);
            $this->pub = $this->datamanager->retrieve_phrases_publication($this->pid);
            $phrases_id = $this->pub->get_content_object();
            $this->phrases = RepositoryDataManager :: get_instance()->retrieve_content_object($phrases_id);
        }

        if (Request :: get(PhrasesManager :: PARAM_INVITATION_ID))
        {
            $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_INVITATION_CODE, Request :: get(PhrasesManager :: PARAM_INVITATION_ID));
            $invitation = $this->datamanager->retrieve_survey_invitations($condition)->next_result();

            $this->pid = $invitation->get_survey_id();
            $this->pub = $this->datamanager->retrieve_phrases_publication($this->pid);
            $phrases_id = $this->pub->get_content_object();
            $this->phrases = RepositoryDataManager :: get_instance()->retrieve_content_object($phrases_id);
        }

        if ($this->pub && ! $this->pub->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed(null, false);
        }

        // Checking statistics
        $conditions[] = new EqualityCondition(PhrasesPhrasesAttemptsTracker :: PROPERTY_PHRASES_ID, $this->pid);
        $conditions[] = new EqualityCondition(PhrasesPhrasesAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition($conditions);

        $trackers = Tracker :: get_data(PhrasesPhrasesAttemptsTracker :: CLASS_NAME, PhrasesManager :: APPLICATION_NAME, $condition);
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

        if ($this->phrases->get_maximum_attempts() != 0 && $count >= $this->phrases->get_maximum_attempts())
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

        // Executing phrases
        if ($this->phrases->get_phrases_type() == Hotpotatoes :: TYPE_HOTPOTATOES)
        {
            $this->display_header();

            $path = $this->phrases->add_javascript(Path :: get(WEB_PATH) . 'application/phrases/php/ajax/hotpotatoes_save_score.php', $this->get_browse_phrases_publications_url(), $this->active_tracker->get_id());
            echo '<iframe src="' . $path . '" width="100%" height="600">
  				 <p>Your browser does not support iframes.</p>
				 </iframe>';
            //require_once $path;
            $this->display_footer();
            exit();
        }
        else
        {
            ComplexDisplay :: launch($this->phrases->get_type(), $this);
        }

    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_viewer');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PHRASES_PUBLICATION, self :: PARAM_INVITATION_ID);
    }

    function get_root_content_object()
    {
        return $this->phrases;
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
        $parameters = array(
                PhrasesPhrasesAttemptsTracker :: PROPERTY_PHRASES_ID => $this->pid,
                PhrasesPhrasesAttemptsTracker :: PROPERTY_USER_ID => $this->get_user_id(),
                PhrasesPhrasesAttemptsTracker :: PROPERTY_TOTAL_SCORE => 0);
        $tracker = Event :: trigger('attempt_phrases', PhrasesManager :: APPLICATION_NAME, $parameters);
        return $tracker[0];
    }

    function save_phrases_answer($complex_question_id, $answer, $score)
    {
        $parameters = array();
        $parameters[PhrasesQuestionAttemptsTracker :: PROPERTY_PHRASES_ATTEMPT_ID] = $this->active_tracker->get_id();
        $parameters[PhrasesQuestionAttemptsTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
        $parameters[PhrasesQuestionAttemptsTracker :: PROPERTY_ANSWER] = $answer;
        $parameters[PhrasesQuestionAttemptsTracker :: PROPERTY_SCORE] = $score;
        $parameters[PhrasesQuestionAttemptsTracker :: PROPERTY_FEEDBACK] = '';

        Event :: trigger('attempt_question', PhrasesManager :: APPLICATION_NAME, $parameters);
    }

    function save_phrases_result($total_score)
    {
        $tracker = $this->active_tracker;

        $tracker->set_total_score($total_score);
        $tracker->set_total_time($tracker->get_total_time() + (time() - $tracker->get_start_time()));
        $tracker->set_status('completed');
        $tracker->update();
    }

    function get_phrases_current_attempt_id()
    {
        return $this->active_tracker->get_id();
    }

    function get_phrases_go_back_url()
    {
        return $this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS,
                PhrasesManager :: PARAM_PHRASES_PUBLICATION => null));
    }

    /**
     * Unused for phrasess
     */
    function is_allowed($right)
    {
        return true;
    }
}
?>
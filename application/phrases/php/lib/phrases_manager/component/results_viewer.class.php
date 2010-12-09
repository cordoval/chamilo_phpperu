<?php
namespace application\phrases;

use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use repository\ComplexDisplay;
use common\libraries\Path;
use reporting\ReportingDataManager;
use common\libraries\AndCondition;
/**
 * $Id: results_viewer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component
 */
require_once dirname(__FILE__) . '/../phrases_manager.class.php';
require_once dirname(__FILE__) . '/../../../trackers/phrases_question_attempts_tracker.class.php';
require_once dirname(__FILE__) . '/../../../trackers/phrases_phrases_attempts_tracker.class.php';

/**
 * Component to create a new phrases_publication object
 * @author Hans De Bisschop
 * @author
 */
class PhrasesManagerResultsViewerComponent extends PhrasesManager
{
    private $current_attempt_id;
    private $object;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->trail = $trail = BreadcrumbTrail :: get_instance();

        $pid = Request :: get(PhrasesManager :: PARAM_PHRASES_PUBLICATION);
        $delete = Request :: get('delete');

        if ($delete)
        {
            $split = explode('_', $delete);
            $id = $split[1];

            if ($split[0] == 'aid')
            {
                $condition = new EqualityCondition(PhrasesPhrasesAttemptsTracker :: PROPERTY_PHRASES_ID, $id);
            }
            else
            {
                $condition = new EqualityCondition(PhrasesPhrasesAttemptsTracker :: PROPERTY_ID, $id);
                $parameters = array(
                        PhrasesManager :: PARAM_PHRASES_PUBLICATION => $pid);
            }

            $dummy = new PhrasesPhrasesAttemptsTracker();
            $trackers = $dummy->retrieve_tracker_items($condition);
            foreach ($trackers as $tracker)
            {
                $tracker->delete();
            }

            $this->redirect(Translation :: get('PhrasesAttemptsDeleted'), false, $parameters);
            exit();
        }

        if (! $pid)
        {
            $this->display_header();
            echo $this->display_summary_results();
            $this->display_footer();
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(array(
                    PhrasesManager :: PARAM_PHRASES_PUBLICATION => $pid)), Translation :: get('ViewPhrasesResults')));

            $details = Request :: get('details');
            if ($details)
            {
                $trail->add(new Breadcrumb($this->get_url(array(
                        PhrasesManager :: PARAM_PHRASES_PUBLICATION => $pid,
                        'details' => $details)), Translation :: get('ViewPhrasesDetails')));

                $this->current_attempt_id = $details;

                $pub = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($pid);
                $object = $pub->get_publication_object();

                $_GET['display_action'] = PhrasesManager :: ACTION_VIEW_PHRASES_PUBLICATION_RESULTS;

                $this->set_parameter('details', $details);
                $this->set_parameter(PhrasesManager :: PARAM_PHRASES_PUBLICATION, $pid);

                $this->object = $object;
                ComplexDisplay :: launch($object->get_type(), $this);
            }
            else
            {
                $this->display_header();
                echo $this->display_phrases_results($pid);
                $this->display_footer();
            }
        }
    }

    function get_root_content_object()
    {
        return $this->object;
    }

    function display_header($trail)
    {
        if ($trail)
        {
            $this->trail->merge($trail);
        }

        parent :: display_header($this->trail);
    }

    function display_summary_results()
    {
        require_once (Path :: get_application_path() . '/phrases/php/reporting/templates/phrases_attempts_summary_template.class.php');

        $current_category = Request :: get('category');
        $current_category = $current_category ? $current_category : 0;
        $parameters = array('category' => $current_category, 'url' => $this->get_url());
        $database = ReportingDataManager :: get_instance();
        $template_obj = $database->retrieve_reporting_template_object('phrases_attempts_summary_template');
        $template = new PhrasesAttemptsSummaryTemplate($this, $template_obj->get_id(), $parameters, null);
        //$template->set_reporting_blocks_function_parameters($parameters);
        return $template->to_html();
    }

    function display_phrases_results($pid)
    {
        require_once (Path :: get_application_path() . '/phrases/php/reporting/templates/phrases_attempts_template.class.php');

        $url = $this->get_url(array(PhrasesManager :: PARAM_PHRASES_PUBLICATION => $pid));
        $results_export_url = $this->get_results_exporter_url();
        $parameters = array(PhrasesManager :: PARAM_PHRASES_PUBLICATION => $pid,
                'url' => $url,
                'results_export_url' => $results_export_url);
        $database = ReportingDataManager :: get_instance();
        $template_obj = $database->retrieve_reporting_template_object('phrases_attempts_template');
        $template = new PhrasesAttemptsTemplate($this, $template_obj->get_id(), $parameters, null, $pid);
        //$template->set_reporting_blocks_function_parameters($parameters);
        return $template->to_html();
    }

    function retrieve_phrases_results()
    {
        $condition = new EqualityCondition(PhrasesQuestionAttemptsTracker :: PROPERTY_PHRASES_ATTEMPT_ID, $this->current_attempt_id);

        $dummy = new PhrasesQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        $results = array();

        foreach ($trackers as $tracker)
        {
            $results[$tracker->get_question_cid()] = array(
                    'answer' => $tracker->get_answer(),
                    'feedback' => $tracker->get_feedback(),
                    'score' => $tracker->get_score());
        }

        return $results;
    }

    function change_answer_data($question_cid, $score, $feedback)
    {
        $conditions[] = new EqualityCondition(PhrasesQuestionAttemptsTracker :: PROPERTY_PHRASES_ATTEMPT_ID, $this->current_attempt_id);
        $conditions[] = new EqualityCondition(PhrasesQuestionAttemptsTracker :: PROPERTY_QUESTION_CID, $question_cid);
        $condition = new AndCondition($conditions);

        $dummy = new PhrasesQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $tracker = $trackers[0];
        $tracker->set_score($score);
        $tracker->set_feedback($feedback);
        $tracker->update();
    }

    function change_total_score($total_score)
    {
        $condition = new EqualityCondition(PhrasesPhrasesAttemptsTracker :: PROPERTY_ID, $this->current_attempt_id);
        $dummy = new PhrasesPhrasesAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $tracker = $trackers[0];

        if (! $tracker)
            return;

        $tracker->set_total_score($total_score);
        $tracker->update();
    }

    function can_change_answer_data()
    {
        return true;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_results_viewer');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PHRASES_PUBLICATION);
    }
}
?>
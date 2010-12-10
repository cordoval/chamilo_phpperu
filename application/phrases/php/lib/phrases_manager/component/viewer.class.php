<?php
namespace application\phrases;

use repository\content_object\assessment;

use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;

use tracking\Tracker;
use tracking\Event;

use repository\ComplexDisplay;
use repository\RepositoryDataManager;

use repository\content_object\assessment\AssessmentComplexDisplaySupport;

use repository\content_object\adaptive_assessment\AdaptiveAssessmentComplexDisplaySupport;
use repository\content_object\adaptive_assessment\AdaptiveAssessmentContentObjectDisplay;
use repository\content_object\adaptive_assessment\AdaptiveAssessmentDisplay;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesManagerViewerComponent extends PhrasesManager implements AdaptiveAssessmentComplexDisplaySupport, AssessmentComplexDisplaySupport
{
    private $publication;

    function run()
    {
        $publication_id = Request :: get(self :: PARAM_PHRASES_PUBLICATION);

        if (! $publication_id)
        {
            $this->redirect(Translation :: get('NoSuchPublication'), true, array(
                    self :: PARAM_ACTION => self :: ACTION_BROWSE_PHRASES_PUBLICATIONS));
        }
        else
        {
            $this->publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($publication_id);

            if ($this->publication && ! $this->publication->is_visible_for_target_user($this->get_user()))
            {
                $this->not_allowed(null, false);
            }
            else
            {
                ComplexDisplay :: launch($this->get_root_content_object()->get_type(), $this);
            }
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
        return array(self :: PARAM_PHRASES_PUBLICATION);
    }


    function get_root_content_object()
    {
        $embedded_content_object_id = AdaptiveAssessmentContentObjectDisplay :: get_embedded_content_object_id();

        if ($embedded_content_object_id)
        {
            $this->set_parameter(AdaptiveAssessmentContentObjectDisplay :: PARAM_EMBEDDED_CONTENT_OBJECT_ID, $embedded_content_object_id);
            return RepositoryDataManager :: get_instance()->retrieve_content_object($embedded_content_object_id);
        }
        else
        {
            $this->set_parameter(AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID, Request :: get(AdaptiveAssessmentDisplay :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID));
            $this->set_parameter(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, Request :: get(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID));
            return $this->publication->get_publication_object();
        }
    }

    function display_header()
    {
        $embedded_content_object_id = AdaptiveAssessmentContentObjectDisplay :: get_embedded_content_object_id();

        if ($embedded_content_object_id)
        {
            Display :: small_header();
        }
        else
        {
            parent :: display_header();
        }
    }

    function display_footer()
    {
        $embedded_content_object_id = AdaptiveAssessmentContentObjectDisplay :: get_embedded_content_object_id();

        if ($embedded_content_object_id)
        {
            Display :: small_footer();
        }
        else
        {
            parent :: display_footer();
        }
    }

    function get_publication()
    {
        return $this->publication;
    }

    /**
     * Not implemented right now
     *
     * @param $right
     * @return boolean
     */
    function is_allowed($right)
    {
        return true;
    }

    /**
     * TODO: Save the assessment question answer
     *
     * @param int $complex_question_id
     * @param mixed $answer
     * @param int $score
     */
    function save_assessment_answer($complex_question_id, $answer, $score)
    {
    }

    /**
     * TODO: Save the assessment result
     *
     * @param int $total_score
     */
    function save_assessment_result($total_score)
    {
    }

    /**
     * TODO: Get the current assessment attempt id
     */
    function get_assessment_current_attempt_id()
    {
    }

    /**
     * TODO: Get a valid go back url
     *
     * @return string
     */
    function get_assessment_go_back_url()
    {
    }

    /**
     * TODO: Provide a tracker with actual data
     *
     * @return PhrasesAdaptiveAssessmentAttemptTracker
     */
    function retrieve_adaptive_assessment_tracker()
    {
        return new PhrasesAdaptiveAssessmentAttemptTracker();
    }

    /**
     * TODO: Provide a tracker-array with actual data
     *
     * @return array
     */
    function retrieve_adaptive_assessment_tracker_items($adaptive_assessment_tracker)
    {
    }

    /**
     * TODO: Provide an actual & valid url
     *
     * @return string
     */
    function get_adaptive_assessment_tree_menu_url()
    {
        return '%s';
    }

    /**
     * @param int $total_steps
     */
    function get_adaptive_assessment_previous_url($total_steps)
    {
        return $this->get_url(array('step' => $total_steps));
    }

    /**
     * TODO: Provide a tracker with actual data
     * Creates an adaptive assessment item tracker
     *
     * @param AdaptiveAssessmentAttemptTracker $adaptive_assessment_tracker
     * @param ComplexContentObjectItem $current_complex_content_object_item
     * @return array AdaptiveAssessmentItemAttemptTracker
     */
    function create_adaptive_assessment_item_tracker($adaptive_assessment_tracker, $current_complex_content_object_item)
    {
        $item_tracker = new PhrasesAdaptiveAssessmentItemAttemptTracker();
        $item_tracker->set_adaptive_assessment_item_id($adaptive_assessment_tracker->get_id());
        return $item_tracker;
    }

    /**
     * TODO: Provide an actual & valid url
     * @param int $complex_content_object_id
     * @return string
     */
    function get_adaptive_assessment_content_object_item_details_url($complex_content_object_id)
    {
        return '';
    }

    /**
     * * TODO: Provide an actual & valid url
     * Get the url of the assessment result
     *
     * @param int $complex_content_object_id
     * @param unknown_type $details
     */
    function get_adaptive_assessment_content_object_assessment_result_url($complex_content_object_id, $details)
    {
        return '';
    }

    /**
     * TODO: Implement reporting and return template name
     * @return string
     */
    function get_adaptive_assessment_attempt_progress_details_reporting_template_name()
    {
        return '';
    }

    /**
     * TODO: Implement reporting and return template name
     * @return string
     */
    function get_adaptive_assessment_attempt_progress_reporting_template_name()
    {
        return '';
    }

    function get_adaptive_assessment_template_application_name()
    {
        return self :: APPLICATION_NAME;
    }
}
?>
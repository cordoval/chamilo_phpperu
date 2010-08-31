<?php
/**
 * $Id: learning_path_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_tree.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_content_object_display.class.php';
require_once dirname(__FILE__) . '/../../../trackers/weblcms_lp_attempt_tracker.class.php';
require_once dirname(__FILE__) . '/../../../trackers/weblcms_lpi_attempt_tracker.class.php';
require_once dirname(__FILE__) . '/../../../trackers/weblcms_lpi_attempt_objective_tracker.class.php';
require_once dirname(__FILE__) . '/../../../trackers/weblcms_learning_path_question_attempts_tracker.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/prerequisites_translator.class.php';

class LearningPathToolAttemptComponent extends LearningPathTool
{
    private $pid;
    private $trackers;
    private $lpi_attempt_data;
    private $cloi;
    private $menu;
    private $navigation;
    private $empty_learning_path;

    private $root_content_object;

    function run()
    {
        // Check for rights
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses learnpath tool');

        // Check and retrieve publication
        $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $this->pid = $pid;

        if (! $pid)
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoObjectSelected'));
            $this->display_footer();
        }

        $dm = WeblcmsDataManager :: get_instance();
        $publication = $dm->retrieve_content_object_publication($pid);
        $root_object = $publication->get_content_object();

        // Do tracking stuff
        $this->trackers['lp_tracker'] = $this->retrieve_lp_tracker($publication);
        $lpi_attempt_data = $this->retrieve_tracker_items($this->trackers['lp_tracker']);

        // Retrieve tree menu
        if (Request :: get('lp_action') == 'view_progress')
        {
            $step = null;
        }
        else
        {
            $step = Request :: get(LearningPathTool :: PARAM_LP_STEP) ? Request :: get(LearningPathTool :: PARAM_LP_STEP) : 1;
        }

        $this->menu = $this->get_menu($root_object->get_id(), $step, $pid, $lpi_attempt_data);
        $object = $this->menu->get_current_object();
        $cloi = $this->menu->get_current_cloi();
        $this->cloi = $cloi;

        // Update main tracker
        $this->trackers['lp_tracker']->set_progress($this->menu->get_progress());
        $this->trackers['lp_tracker']->update();

        //$trail->merge($this->menu->get_breadcrumbs());


        $this->navigation = $this->get_navigation_menu($this->menu->count_steps(), $step, $object, $this->menu);
        $objects = $this->menu->get_objects();

        // Retrieve correct display and show it on screen
        if (Request :: get('lp_action') == 'view_progress')
        {
            $url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, Tool :: PARAM_PUBLICATION_ID => $pid, 'lp_action' => 'view_progress'));
            require_once (Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_attempt_progress_reporting_template.class.php');
            require_once (Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_attempt_progress_details_reporting_template.class.php');

            $cid = Request :: get('cid');
            $details = Request :: get('details');

            if ($cid)
            {
                $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, Tool :: PARAM_PUBLICATION_ID => $pid, 'lp_action' => 'view_progress', 'cid' => $cid, 'attempt_id' => Request :: get('attempt_id'))), Translation :: get('ItemDetails')));
            }

            if ($details)
            {
                $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, Tool :: PARAM_PUBLICATION_ID => $pid, 'lp_action' => 'view_progress', 'cid' => $cid, 'details' => $details)), Translation :: get('AssessmentResult')));

                $this->set_parameter('tool_action', 'view');
                $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $pid);
                $this->set_parameter('lp_action', 'view_progress');
                $this->set_parameter('cid', $cid);
                $this->set_parameter('details', $details);
                $_GET['display_action'] = 'view_result';

                $object = $objects[$cid];

                $this->root_content_object = $object;
                ComplexDisplay :: launch($object->get_type(), $this);
            }
            else
            {
                $rtv = ReportingViewer :: construct($this);
                $rtv->set_breadcrumb_trail($trail);
                $rtv->show_all_blocks();
                if ($cid)
                {
                    $rtv->add_template_by_name('learning_path_attempt_progress_details_reporting_template', WeblcmsManager :: APPLICATION_NAME);
                }
                else
                {

                    $rtv->add_template_by_name('learning_path_attempt_progress_reporting_template', WeblcmsManager :: APPLICATION_NAME);
                }
                $rtv->run();
            }
        }
        else
        {
            if ($cloi && get_class($cloi) == "ComplexLearningPathItem")
            {

                if ($root_object->get_version() != 'SCORM2004')
                {
                    $translator = new PrerequisitesTranslator($lpi_attempt_data, $objects, $root_object->get_version());
                    if (! $translator->can_execute_item($cloi))
                    {
                        $this->display_header();
                        $display = '<div class="error-message">' . Translation :: get('NotYetAllowedToView') . '</div>';
                        $this->display_footer();
                        exit();
                    }
                }

                $lpi_tracker = $this->menu->get_current_tracker();
                if (! $lpi_tracker)
                {
                    $lpi_tracker = $this->create_lpi_tracker($this->trackers['lp_tracker'], $cloi);
                    $lpi_attempt_data[$cloi->get_id()]['active_tracker'] = $lpi_tracker;
                }
                else
                {
                    $lpi_tracker->set_start_time(time());
                    $lpi_tracker->update();
                }

                $this->trackers['lpi_tracker'] = $lpi_tracker;

                $this->display_header();
                echo LearningPathContentObjectDisplay :: factory($this, $object->get_type())->display_content_object($object, $lpi_attempt_data[$cloi->get_id()], $this->menu->get_continue_url(), $this->menu->get_previous_url(), $this->menu->get_jump_urls());
                $this->display_footer();
            }
            else
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('EmptyLearningPath'));
                $this->display_footer();
            }
        }
    }

    function display_header()
    {
        parent :: display_header();
        echo '<div style="width: 17%; overflow: auto; float: left;">';
        echo $this->menu->render_as_tree() . '<br /><br />';
        echo $this->get_progress_bar($this->menu->get_progress());
        echo $this->navigation . '<br /><br />';
        echo '</div>';
        echo '<div style="width: 82%; float: right; padding-left: 10px; min-height: 500px;">';
        if (Request :: get('lp_action') == 'view_progress')
        {

        }
    }

    function display_footer()
    {
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';
        parent :: display_footer();
    }

    /**
     * Creates the tree menu for the learning path
     *
     * @param int $root_object_id
     * @param int $selected_object_id
     * @param int $pid
     * @param LearningPathAttemptTracker $lp_tracker
     * @return HTML code of the menu
     */
    private function get_menu($root_object_id, $selected_object_id, $pid, $lp_tracker)
    {
        $this->menu = new LearningPathTree($root_object_id, $selected_object_id, Path :: get(WEB_PATH) . 'run.php?go=courseviewer&course=' . Request :: get('course') . '&application=weblcms&tool=learning_path&tool_action=display&publication=' . $pid . '&' . LearningPathTool :: PARAM_LP_STEP . '=%s', $lp_tracker);

        return $this->menu;
    }

    // Getters & Setters


    function get_publication_id()
    {
        return $this->pid;
    }

    function get_trackers()
    {
        return $this->trackers;
    }

    function get_cloi()
    {
        return $this->cloi;
    }

    // Layout functionality


    /**
     * Retrieves the navigation menu for the learning path
     *
     * @param int $total_steps
     * @param int $current_step
     * @param ContentObject - The current object
     * @return HTML of the navigation menu
     */
    private function get_navigation_menu($total_steps, $current_step, $object, $menu)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if (! $current_step)
        {
            $previous_url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, LearningPathTool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), 'step' => $total_steps));

            $toolbar->add_item(new ToolbarItem(Translation :: get('Previous'), Theme :: get_common_image_path() . 'action_prev.png', $previous_url, ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(new ToolbarItem(Translation :: get('NextNA'), Theme :: get_common_image_path() . 'action_next_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }
        else
        {

            if (get_class($object) == 'ScormItem')
            {
                $hide_lms_ui = $object->get_hide_lms_ui();
            }

            if (! $hide_lms_ui)
                $hide_lms_ui = array($hide_lms_ui);

            $add_previous_na = false;

            if ($current_step > 1 && $this->menu->get_previous_url())
            {
                //$previous_url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), 'step' => $current_step - 1));
                $previous_url = $this->menu->get_previous_url();

                if (! in_array('previous', $hide_lms_ui))
                {
                    $toolbar->add_item(new ToolbarItem(Translation :: get('Previous'), Theme :: get_common_image_path() . 'action_prev.png', $previous_url, ToolbarItem :: DISPLAY_ICON));
                }
                else
                {
                    $add_previous_na = true;
                }
            }
            else
            {
                $add_previous_na = true;
            }

            if ($add_previous_na)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('PreviousNA'), Theme :: get_common_image_path() . 'action_prev_na.png', null, ToolbarItem :: DISPLAY_ICON));
            }

            $add_continue_na = false;

            /*if (($current_step < $total_steps))
            {
                //$continue_url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), 'step' => $current_step + 1));

                $continue_url = $this->menu->get_continue_url();

                if (! in_array('continue', $hide_lms_ui))
                {
                    $toolbar->add_item(new ToolbarItem(
			        		Translation :: get('Next'),
			        		Theme :: get_common_image_path() . 'action_next.png',
			        		$continue_url,
			        		ToolbarItem :: DISPLAY_ICON
			        ));
                }
                else
                {
                    $add_continue_na = true;
                }
            }
            else
            {
                //$continue_url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), 'lp_action' => 'view_progress'));

                $continue_url = $this->menu->get_continue_url();

                if (! in_array('continue', $hide_lms_ui))
                {
                    $toolbar->add_item(new ToolbarItem(
			        		Translation :: get('Next'),
			        		Theme :: get_common_image_path() . 'action_next.png',
			        		$continue_url,
			        		ToolbarItem :: DISPLAY_ICON
			        ));
                }
                else
                {
                    $add_continue_na = true;
                }
            }*/

            $continue_url = $this->menu->get_continue_url();

            if (! in_array('continue', $hide_lms_ui) && $total_steps > 0)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Next'), Theme :: get_common_image_path() . 'action_next.png', $continue_url, ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('NextNA'), Theme :: get_common_image_path() . 'action_next_na.png', null, ToolbarItem :: DISPLAY_ICON));
            }

        }

        return $toolbar->as_html();
    }

    /**
     * Retrieves the progress bar for the learning path
     *
     * @param int $progress - The current progress
     * @return HTML code of the progress bar
     */
    private function get_progress_bar($progress)
    {
        $html[] = '<div style="position: relative; text-align: center; border: 1px solid black; height: 14px; width:100px;">';
        $html[] = '<div style="background-color: lightblue; height: 14px; width:' . $progress . 'px; text-align: center;">';
        $html[] = '</div>';
        $html[] = '<div style="width: 100px; text-align: center; position: absolute; top: 0px;">' . round($progress) . '%</div></div>';

        return implode("\n", $html);
    }

    // Tracker functionality


    /**
     * Retrieves the learning path tracker for the current user
     * @param LearningPath $lp
     * @return A LearningPathAttemptTracker
     */
    private function retrieve_lp_tracker($lp)
    {
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $lp->get_id());
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_USER_ID, $this->get_user_id());
        //$conditions[] = new NotCondition(new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_PROGRESS, 100));
        $condition = new AndCondition($conditions);

        $dummy = new WeblcmsLpAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        $lp_tracker = $trackers[0];

        if (! $lp_tracker)
        {
            $parameters = array();
            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_USER_ID] = $this->get_user_id();
            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID] = $this->get_course_id();
            $parameters[WeblcmsLpAttemptTracker :: PROPERTY_LP_ID] = $lp->get_id();

            $return = Event :: trigger('attempt_learning_path', WeblcmsManager :: APPLICATION_NAME, $parameters);
            $lp_tracker = $return[0];
        }

        return $lp_tracker;
    }

    /**
     * Retrieve the tracker items for the current LearningPathAttemptTracker
     *
     * @param LearningPathAttemptTracker $lp_tracker
     * @return array of LearningPathItemAttemptTracker
     */
    private function retrieve_tracker_items($lp_tracker)
    {
        $lpi_attempt_data = array();

        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID, $lp_tracker->get_id());

        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        foreach ($trackers as $tracker)
        {
            $item_id = $tracker->get_lp_item_id();
            if (! $lpi_attempt_data[$item_id])
            {
                $lpi_attempt_data[$item_id]['score'] = 0;
                $lpi_attempt_data[$item_id]['time'] = 0;
            }

            $lpi_attempt_data[$item_id]['trackers'][] = $tracker;
            $lpi_attempt_data[$item_id]['size'] ++;
            $lpi_attempt_data[$item_id]['score'] += $tracker->get_score();
            if ($tracker->get_total_time())
                $lpi_attempt_data[$item_id]['time'] += $tracker->get_total_time();

            if ($tracker->get_status() == 'completed' || $tracker->get_status() == 'passed')
                $lpi_attempt_data[$item_id]['completed'] = 1;
            else
                $lpi_attempt_data[$item_id]['active_tracker'] = $tracker;
        }
        //dump($lpi_attempt_data);
        return $lpi_attempt_data;

    }

    /**
     * Creates a learning path item tracker
     *
     * @param LearningPathAttemptTracker $lp_tracker
     * @param ComplexContentObjectItem $current_cloi
     * @return array LearningPathItemAttemptTracker
     */
    private function create_lpi_tracker($lp_tracker, $current_cloi)
    {
        $parameters = array();
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID] = $lp_tracker->get_id();
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_LP_ITEM_ID] = $current_cloi->get_id();
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_START_TIME] = time();
        $parameters[WeblcmsLpiAttemptTracker :: PROPERTY_STATUS] = 'not attempted';

        $return = Event :: trigger('attempt_learning_path_item', WeblcmsManager :: APPLICATION_NAME, $parameters);
        $lpi_tracker = $return[0];

        return $lpi_tracker;
    }

    function retrieve_assessment_results()
    {
        $condition = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_LPI_ATTEMPT_ID, Request :: get('details'));

        $dummy = new WeblcmsLearningPathQuestionAttemptsTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        $results = array();

        foreach ($trackers as $tracker)
        {
            $results[$tracker->get_question_cid()] = array('answer' => $tracker->get_answer(), 'feedback' => $tracker->get_feedback(), 'score' => $tracker->get_score());
        }

        return $results;
    }

    function can_change_answer_data()
    {
        return false;
    }

    function get_root_content_object()
    {
        return $this->root_content_object;
    }
}
?>

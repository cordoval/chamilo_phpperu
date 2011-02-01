<?php
namespace repository\content_object\learning_path;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;

use repository\content_object\scorm_item\ScormItem;
use repository\content_object\learning_path_item\ComplexLearningPathItem;

use common\extensions\reporting_viewer\ReportingViewer;

require_once dirname(__FILE__) . '/../learning_path_tree.class.php';
require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';
require_once dirname(__FILE__) . '/../rule_condition_translator.class.php';
require_once dirname(__FILE__) . '/../prerequisites_translator.class.php';

class LearningPathDisplayViewerComponent extends LearningPathDisplay
{
    private $learning_path_trackers;
    private $learning_path_menu;
    private $navigation;
    //    private $empty_learning_path;


    const TRACKER_LEARNING_PATH = 'tracker_learning_path';
    const TRACKER_LEARNING_PATH_ITEM = 'tracker_learning_path_item';

    function run()
    {
        $show_progress = Request :: get(self :: PARAM_SHOW_PROGRESS);
        $learning_path = $this->get_parent()->get_root_content_object();

        $trail = BreadcrumbTrail :: get_instance();

        if (! $learning_path)
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoObjectSelected'));
            $this->display_footer();
        }

        // Process some tracking
        $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH] = $this->get_parent()->retrieve_learning_path_tracker();
        $learning_path_item_attempt_data = $this->get_parent()->retrieve_learning_path_tracker_items($this->learning_path_trackers[self :: TRACKER_LEARNING_PATH]);

        // Retrieve the learning path tree menu
        if ($show_progress)
        {
            $current_step = null;
        }
        else
        {
            $current_step = Request :: get(self :: PARAM_STEP) ? Request :: get(self :: PARAM_STEP) : 1;
        }

        $this->learning_path_menu = new LearningPathTree($learning_path->get_id(), $current_step, $this->get_parent()->get_learning_path_tree_menu_url(), $learning_path_item_attempt_data);

        // Get the currently displayed content object
        $current_content_object = $this->learning_path_menu->get_current_object();
        $this->set_complex_content_object_item($this->learning_path_menu->get_current_cloi());

        // Update the main tracker
        $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH]->set_progress($this->learning_path_menu->get_progress());
        $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH]->update();

        $this->navigation_bar = $this->get_navigation_bar($current_step, $current_content_object);
        $content_objects = $this->learning_path_menu->get_objects();

        // Show the progress if so requested or get the correct content object display and render it
        if ($show_progress)
        {
            $complex_content_object_item_id = $this->get_complex_content_object_item_id();
            $details = Request :: get(self :: PARAM_DETAILS);

            if ($complex_content_object_item_id)
            {
                $trail->add(new Breadcrumb($this->get_parent()->get_learning_path_content_object_item_details_url($complex_content_object_item_id), Translation :: get('ItemDetails')));
            }

            if ($details)
            {
                $trail->add(new Breadcrumb($this->get_parent()->get_learning_path_content_object_assessment_result_url($complex_content_object_item_id, $details), Translation :: get('AssessmentResult')));

                //$this->set_parameter('tool_action', 'view');
                //$this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $pid);
                //$this->set_parameter('lp_action', 'view_progress');
                //$this->set_parameter('cid', $cid);
                //$this->set_parameter('details', $details);
                //$_GET['display_action'] = 'view_result';
                //
                //$object = $objects[$cid];
                //
                //$this->root_content_object = $object;
                //ComplexDisplay :: launch($object->get_type(), $this);
            }
            else
            {
                $rtv = ReportingViewer :: construct($this);
                $rtv->set_breadcrumb_trail($trail);
                $rtv->show_all_blocks();
                if ($complex_content_object_item_id)
                {
                    $rtv->add_template_by_name($this->get_parent()->get_learning_path_attempt_progress_details_reporting_template_name(), $this->get_parent()->get_learning_path_template_application_name());
                }
                else
                {

                    $rtv->add_template_by_name($this->get_parent()->get_learning_path_attempt_progress_reporting_template_name(), $this->get_parent()->get_learning_path_template_application_name());
                }
                $rtv->run();
            }
        }
        else
        {
            if ($this->get_complex_content_object_item() && $this->get_complex_content_object_item() instanceof ComplexLearningPathItem)
            {
                if ($learning_path->get_version() != 'SCORM2004')
                {
                    $translator = new PrerequisitesTranslator($learning_path_item_attempt_data, $content_objects, $learning_path->get_version());
                    if (! $translator->can_execute_item($this->get_complex_content_object_item()))
                    {
                        $this->display_header();
                        echo '<div class="error-message">' . Translation :: get('NotYetAllowedToView') . '</div>';
                        $this->display_footer();
                        exit();
                    }
                }

                $learning_path_item_tracker = $this->learning_path_menu->get_current_tracker();
                if (! $learning_path_item_tracker)
                {
                    $learning_path_item_tracker = $this->get_parent()->create_learning_path_item_tracker($this->learning_path_trackers[self :: TRACKER_LEARNING_PATH], $this->get_complex_content_object_item());
                    $learning_path_item_attempt_data[$this->get_complex_content_object_item_id()]['active_tracker'] = $learning_path_item_tracker;
                }
                else
                {
                    $learning_path_item_tracker->set_start_time(time());
                    $learning_path_item_tracker->update();
                }

                $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH_ITEM] = $learning_path_item_tracker;

                $this->set_parameter(self :: PARAM_LEARNING_PATH_ITEM_ID, $learning_path_item_attempt_data[$this->get_complex_content_object_item_id()]['active_tracker']->get_id());
                $this->set_parameter(self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $this->get_complex_content_object_item_id());

                $this->display_header();
                echo LearningPathContentObjectDisplay :: factory($this, $current_content_object->get_type())->display_content_object($current_content_object, $learning_path_item_attempt_data[$this->get_complex_content_object_item_id()], $this->learning_path_menu->get_continue_url(), $this->learning_path_menu->get_previous_url(), $this->learning_path_menu->get_jump_urls());
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
        echo $this->learning_path_menu->render_as_tree() . '<br /><br />';
        echo $this->get_progress_bar();
        echo $this->navigation_bar . '<br /><br />';
        echo '</div>';
        echo '<div style="width: 82%; float: right; padding-left: 10px; min-height: 500px;">';
    }

    function display_footer()
    {
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';
        parent :: display_footer();
    }

    /**
     * Retrieves the navigation menu for the learning path
     *
     * @param int $total_steps
     * @param int $current_step
     * @param ContentObject $current_content_object
     */
    private function get_navigation_bar($current_step, $current_content_object)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if (! $current_step)
        {
            $previous_url = $this->get_parent()->get_learning_path_previous_url($this->learning_path_menu->count_steps());

            $toolbar->add_item(new ToolbarItem(Translation :: get('Previous'), Theme :: get_common_image_path() . 'action_prev.png', $previous_url, ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(new ToolbarItem(Translation :: get('NextNA'), Theme :: get_common_image_path() . 'action_next_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            if ($current_content_object instanceof ScormItem)
            {
                $hide_lms_ui = $current_content_object->get_hide_lms_ui();
            }

            if (! $hide_lms_ui)
            {
                $hide_lms_ui = array($hide_lms_ui);
            }

            $add_previous_na = false;

            if ($current_step > 1 && $this->learning_path_menu->get_previous_url())
            {
                $previous_url = $this->learning_path_menu->get_previous_url();

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

            $continue_url = $this->learning_path_menu->get_continue_url();

            if (! in_array('continue', $hide_lms_ui) && $this->learning_path_menu->count_steps() > 0)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Next'), Theme :: get_common_image_path() . 'action_next.png', $continue_url, ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('NextNA'), Theme :: get_common_image_path() . 'action_next_na.png', null, ToolbarItem :: DISPLAY_ICON));
            }
        }

        $html = array();
        $html[] = '<div style="text-align: center; width:100px; margin: 10px 0px 0px 0px;">';
        $html[] = '';
        $html[] = '';
        $html[] = $toolbar->as_html();
        $html[] = '';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     * Renders the progress bar for the learning path
     *
     * @return array() HTML code of the progress bar
     */
    private function get_progress_bar()
    {
        $progress = $this->learning_path_menu->get_progress();

        $html = array();
        $html[] = '<div style="position: relative; text-align: center; border: 1px solid black; height: 14px; width:100px;">';
        $html[] = '<div style="background-color: lightblue; height: 14px; width:' . $progress . 'px; text-align: center;">';
        $html[] = '</div>';
        $html[] = '<div style="width: 100px; text-align: center; position: absolute; top: 0px;">' . round($progress) . '%</div></div>';

        return implode("\n", $html);
    }

    function get_learning_path_trackers()
    {
        return $this->learning_path_trackers;
    }
}
?>
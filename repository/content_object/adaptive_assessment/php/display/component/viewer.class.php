<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;

use repository\ContentObject;

use repository\content_object\scorm_item\ScormItem;
use repository\content_object\adaptive_assessment_item\ComplexAdaptiveAssessmentItem;

use common\extensions\reporting_viewer\ReportingViewer;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentDisplayViewerComponent extends AdaptiveAssessmentDisplay
{
    private $adaptive_assessment_trackers;
    private $adaptive_assessment_menu;
    private $navigation;
    //    private $empty_adaptive_assessment;


    const TRACKER_LEARNING_PATH = 'tracker_adaptive_assessment';
    const TRACKER_LEARNING_PATH_ITEM = 'tracker_adaptive_assessment_item';

    function run()
    {
        $show_progress = Request :: get(self :: PARAM_SHOW_PROGRESS);
        $adaptive_assessment = $this->get_parent()->get_root_content_object();

        $trail = BreadcrumbTrail :: get_instance();

        if (! $adaptive_assessment)
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NoObjectSelected'));
            $this->display_footer();
        }

        // Process some tracking
        $this->adaptive_assessment_trackers[self :: TRACKER_LEARNING_PATH] = $this->get_parent()->retrieve_adaptive_assessment_tracker();
        $adaptive_assessment_item_attempt_data = $this->get_parent()->retrieve_adaptive_assessment_tracker_items($this->adaptive_assessment_trackers[self :: TRACKER_LEARNING_PATH]);

        // Retrieve the learning path tree menu
        if ($show_progress)
        {
            $current_step = null;
        }
        else
        {
            $current_step = Request :: get(self :: PARAM_STEP) ? Request :: get(self :: PARAM_STEP) : 1;
        }

        $this->adaptive_assessment_menu = new AdaptiveAssessmentTree($adaptive_assessment->get_id(), $current_step, $this->get_parent()->get_adaptive_assessment_tree_menu_url(), $adaptive_assessment_item_attempt_data);

        // Get the currently displayed content object
        $current_content_object = $this->adaptive_assessment_menu->get_current_object();
        $this->set_complex_content_object_item($this->adaptive_assessment_menu->get_current_cloi());

        // Update the main tracker
        $this->adaptive_assessment_trackers[self :: TRACKER_LEARNING_PATH]->set_progress($this->adaptive_assessment_menu->get_progress());
        $this->adaptive_assessment_trackers[self :: TRACKER_LEARNING_PATH]->update();

        $this->navigation_bar = $this->get_navigation_bar($current_step, $current_content_object);
        $content_objects = $this->adaptive_assessment_menu->get_objects();

        // Show the progress if so requested or get the correct content object display and render it
        if ($show_progress)
        {
            $complex_content_object_item_id = $this->get_complex_content_object_item_id();
            $details = Request :: get(self :: PARAM_DETAILS);

            if ($complex_content_object_item_id)
            {
                $trail->add(new Breadcrumb($this->get_parent()->get_adaptive_assessment_content_object_item_details_url($complex_content_object_item_id), Translation :: get('ItemDetails')));
            }

            if ($details)
            {
                $trail->add(new Breadcrumb($this->get_parent()->get_adaptive_assessment_content_object_assessment_result_url($complex_content_object_item_id, $details), Translation :: get('AssessmentResult')));
            }
            else
            {
                $this->display_header();
                $this->display_message(Translation :: get('ComingSoon'));
                $this->display_footer();
//                $rtv = ReportingViewer :: construct($this);
//                $rtv->set_breadcrumb_trail($trail);
//                $rtv->show_all_blocks();
//                if ($complex_content_object_item_id)
//                {
//                    $rtv->add_template_by_name($this->get_parent()->get_adaptive_assessment_attempt_progress_details_reporting_template_name(), $this->get_parent()->get_adaptive_assessment_template_application_name());
//                }
//                else
//                {
//
//                    $rtv->add_template_by_name($this->get_parent()->get_adaptive_assessment_attempt_progress_reporting_template_name(), $this->get_parent()->get_adaptive_assessment_template_application_name());
//                }
//                $rtv->run();
            }
        }
        else
        {
            if ($this->get_complex_content_object_item() && $this->get_complex_content_object_item() instanceof ComplexAdaptiveAssessmentItem)
            {

                $translator = new PrerequisitesTranslator($adaptive_assessment_item_attempt_data, $content_objects);
                if (! $translator->can_execute_item($this->get_complex_content_object_item()))
                {
                    $this->display_header();
                    $display = '<div class="error-message">' . Translation :: get('NotYetAllowedToView') . '</div>';
                    $this->display_footer();
                    exit();
                }

                $adaptive_assessment_item_tracker = $this->adaptive_assessment_menu->get_current_tracker();
                if (! $adaptive_assessment_item_tracker)
                {
                    $adaptive_assessment_item_tracker = $this->get_parent()->create_adaptive_assessment_item_tracker($this->adaptive_assessment_trackers[self :: TRACKER_LEARNING_PATH], $this->get_complex_content_object_item());
                    $adaptive_assessment_item_attempt_data[$this->get_complex_content_object_item_id()]['active_tracker'] = $adaptive_assessment_item_tracker;
                }
                else
                {
                    $adaptive_assessment_item_tracker->set_start_time(time());
                    $adaptive_assessment_item_tracker->update();
                }

                $this->adaptive_assessment_trackers[self :: TRACKER_LEARNING_PATH_ITEM] = $adaptive_assessment_item_tracker;

                $this->set_parameter(self :: PARAM_ADAPTIVE_ASSESSMENT_ITEM_ID, $adaptive_assessment_item_attempt_data[$this->get_complex_content_object_item_id()]['active_tracker']->get_id());
                $this->set_parameter(self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $this->get_complex_content_object_item_id());

                $this->display_header();
                echo AdaptiveAssessmentContentObjectDisplay :: factory($this, $current_content_object->get_type())->display_content_object($current_content_object, $adaptive_assessment_item_attempt_data[$this->get_complex_content_object_item_id()], $this->adaptive_assessment_menu->get_continue_url(), $this->adaptive_assessment_menu->get_previous_url(), $this->adaptive_assessment_menu->get_jump_urls());
                $this->display_footer();
            }
            elseif ($this->get_complex_content_object_item() && $this->get_complex_content_object_item() instanceof ComplexAdaptiveAssessment)
            {
                $this->display_header();

                $html = array();
                $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($current_content_object->get_type())) . 'logo/' . $current_content_object->get_icon_name() . ($current_content_object->is_latest_version() ? '' : '_na') . '.png);">';
                $html[] = '<div class="title">' . $current_content_object->get_title() . '</div>';
                $html[] = '<div class="description" style="overflow: auto;">';
                $html[] = '<div class="description">';
                $html[] = $current_content_object->get_description();
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';
                echo implode("\n", $html);

                $this->display_footer();
            }
            else
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('EmptyAdaptiveAssessment'));
                $this->display_footer();
            }
        }
    }

    function display_header()
    {
        parent :: display_header();
        echo '<div style="width: 17%; overflow: auto; float: left;">';
        echo $this->adaptive_assessment_menu->render_as_tree() . '<br /><br />';
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
            $previous_url = $this->get_parent()->get_adaptive_assessment_previous_url($this->adaptive_assessment_menu->count_steps());

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

            if ($current_step > 1 && $this->adaptive_assessment_menu->get_previous_url())
            {
                $previous_url = $this->adaptive_assessment_menu->get_previous_url();

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

            $continue_url = $this->adaptive_assessment_menu->get_continue_url();

            if (! in_array('continue', $hide_lms_ui) && $this->adaptive_assessment_menu->count_steps() > 0)
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
        $progress = $this->adaptive_assessment_menu->get_progress();

        $html = array();
        $html[] = '<div style="position: relative; text-align: center; border: 1px solid black; height: 14px; width:100px;">';
        $html[] = '<div style="background-color: lightblue; height: 14px; width:' . $progress . 'px; text-align: center;">';
        $html[] = '</div>';
        $html[] = '<div style="width: 100px; text-align: center; position: absolute; top: 0px;">' . round($progress) . '%</div></div>';

        return implode("\n", $html);
    }

    function get_adaptive_assessment_trackers()
    {
        return $this->adaptive_assessment_trackers;
    }
}
?>
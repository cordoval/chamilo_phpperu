<?php
/**
 * $Id: object_publication_table_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.object_publication_table
 */
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/object_publication_table_column_model.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_assessment_attempts_tracker.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class ObjectPublicationTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    protected $browser;
    private $object_count;

    function ObjectPublicationTableCellRenderer($browser)
    {
        $this->browser = $browser;
    }

    function set_object_count($count)
    {
        $this->object_count = $count;
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        if ($column === ObjectPublicationTableColumnModel :: get_action_column())
        {
            return Utilities :: build_toolbar($this->get_actions($publication));
        }

        switch ($column->get_name())
        {
            case ContentObjectPublication :: PROPERTY_PUBLICATION_DATE :
                $date_format = Translation :: get('dateTimeFormatLong');
                $data = DatetimeUtilities :: format_locale_date($date_format, $publication->get_publication_date());
                break;
            case ContentObjectPublication :: PROPERTY_PUBLISHER_ID :
                $user = $this->retrieve_user($publication->get_publisher_id());
                $data = $user->get_fullname();
                break;
            case 'published_for' :
                $data = $this->render_publication_targets($publication);
                break;
            case ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX:
            	return $publication->get_display_order_index();

        }

        if (! $data)
            $data = parent :: render_cell($column, $publication->get_content_object());

        if ($publication->is_hidden())
        {
            return '<span style="color: gray">' . $data . '</span>';
        }
        else
        {
            return $data;
        }
    }

    function render_publication_targets($publication)
    {
        if ($publication->is_email_sent())
        {
            $email_suffix = ' - <img src="' . Theme :: get_common_image_path() . 'action_email.png" alt="" style="vertical-align: middle;"/>';
        }
        if ($publication->is_for_everybody())
        {
            return htmlentities(Translation :: get('Everybody')) . $email_suffix;
        }
        else
        {
            $users = $publication->get_target_users();
            $course_groups = $publication->get_target_course_groups();
            $groups = $publication->get_target_groups();
            if (count($users) + count($course_groups) + count($groups) == 1)
            {
                if (count($users) == 1)
                {
                    $user = $this->retrieve_user($users[0]);
                    return $user->get_firstname() . ' ' . $user->get_lastname() . $email_suffix;
                }
                elseif(count($groups) == 1)
                {
                    $gdm = GroupDataManager :: get_instance();
                    $group = $gdm->retrieve_group($groups[0]);
                    return $group->get_name();
                }
                else
                {
                    $wdm = WeblcmsDatamanager :: get_instance();
                    $course_group = $wdm->retrieve_course_group($course_groups[0]);
                    return $course_group->get_name();
                }
            }
            $target_list = array();
            $target_list[] = '<select>';
            foreach ($users as $user_id)
            {
                $user = $this->retrieve_user($user_id);
                $target_list[] = '<option>' . $user->get_fullname() . '</option>';
            }
            foreach ($course_groups as $course_group_id)
            {
                $wdm = WeblcmsDatamanager :: get_instance();
                $course_group = $wdm->retrieve_course_group($course_group_id);
                $target_list[] = '<option>' . $course_group->get_name() . '</option>';
            }
            foreach ($groups as $index => $group_id)
            {
                $gdm = GroupDataManager :: get_instance();
                //Todo: make this more efficient. Get all course_groups using a single query
                $group = $gdm->retrieve_group($group_id);
                $target_list[] = '<option>' . $group->get_name() . '</option>';
            }
            $target_list[] = '</select>';
            return implode("\n", $target_list) . $email_suffix;
        }
    }

    function retrieve_user($user_id)
    {
        return UserDataManager :: get_instance()->retrieve_user($user_id);
    }

    function get_actions($publication)
    {
        if ($this->browser->is_allowed(EDIT_RIGHT))
        {
            $actions['delete'] = array('href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');

            $actions['edit'] = array('href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');

            if ($publication->get_content_object()->is_complex_content_object())
            {
                $actions['build'] = array('href' => $this->browser->get_complex_builder_url($publication->get_id()), 'label' => Translation :: get('BuildComplex'), 'img' => Theme :: get_common_image_path() . 'action_bar.png');
            }

            $img = 'action_visible.png';
            if ($publication->is_hidden())
            {
                $img = 'action_visible_na.png';
            }

            if($publication->get_display_order_index() > 1)
            {
            	$actions[] = array(
            		'href' => $this->browser->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_UP, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())),
            		'label' => Translation :: get('Up'),
            		'img' => Theme :: get_common_image_path() . 'action_up.png'
            	);
            }
            else
            {
            	$actions[] = array(
            		'label' => Translation :: get('UpNA'),
            		'img' => Theme :: get_common_image_path() . 'action_up_na.png'
            	);
            }

            if($publication->get_display_order_index() < $this->object_count)
            {
            	$actions[] = array(
            		'href' => $this->browser->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_DOWN, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())),
            		'label' => Translation :: get('Down'),
            		'img' => Theme :: get_common_image_path() . 'action_down.png'
            	);
            }
            else
            {
            	$actions[] = array(
            		'label' => Translation :: get('DownNA'),
            		'img' => Theme :: get_common_image_path() . 'action_down_na.png'
            	);
            }

            $actions['visible'] = array('href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 'label' => Translation :: get('Visible'), 'img' => Theme :: get_common_image_path() . $img);

            $actions['move'] = array('href' => $this->browser->get_url(array(AssessmentTool :: PARAM_ACTION => Tool :: ACTION_MOVE_TO_CATEGORY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 'label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move.png');

        }

        $feedback_url = $this->browser->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => 'view'));
        $actions['feedback'] = array('href' => $feedback_url, 'label' => Translation :: get('Feedback'), 'img' => Theme :: get_common_image_path() . 'action_browser.png');
        
        if(WebApplication :: is_active('gradebook'))
        {
        	require_once dirname (__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';
        	$internal_item = EvaluationManager :: retrieve_internal_item_by_publication(WeblcmsManager :: APPLICATION_NAME, $publication->get_id());
        	if($internal_item && $internal_item->get_calculated() != 1)
        	{
        		$evaluate_url = $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EVALUATE_TOOL_PUBLICATION, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()));
				$actions['evaluate'] = array('href' => $evaluate_url, 'label' => Translation :: get('Evaluate'), 'img' => Theme :: get_common_image_path() . 'action_evaluation.png'); 
        	}
        }
        return $actions;
    }
}
?>
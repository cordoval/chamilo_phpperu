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
    protected $table_renderer;
    private $object_count;

    function ObjectPublicationTableCellRenderer($table_renderer)
    {
        $this->table_renderer = $table_renderer;
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
            return $this->get_actions($publication)->as_html();
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
        return $this->table_renderer->get_publication_actions($publication);
    }
}
?>
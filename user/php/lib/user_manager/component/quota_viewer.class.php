<?php
/**
 * $Id: quota_viewer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */
/**
 * User manager component which displays the quota to the user.
 *
 * This component displays two progress-bars. The first one displays the used
 * disk space and the second one the number of learning objects in the users
 * user.
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class UserManagerQuotaViewerComponent extends UserManager implements AdministrationComponent
{
    private $selected_user;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $selected_user_id = Request :: get(UserManager :: PARAM_USER_USER_ID);
        if (! $selected_user_id)
            $this->selected_user = $this->get_user();
        else
            $this->selected_user = UserDataManager :: get_instance()->retrieve_user($selected_user_id);

        if (!UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, $this->selected_user->get_id()))
        {
        	$this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
            
        $this->display_header();

        $this->display_action_bar();

        $quotamanager = new QuotaManager($this->selected_user);
        echo '<h3>' . htmlentities(Translation :: get('DiskSpace')) . '</h3>';
        echo self :: get_bar($quotamanager->get_used_disk_space_percent(), Filesystem :: format_file_size($quotamanager->get_used_disk_space()) . ' / ' . Filesystem :: format_file_size($quotamanager->get_max_disk_space()));
        echo '<div style="clear: both;">&nbsp;</div>';
        echo '<h3>' . htmlentities(Translation :: get('NumberOfContentObjects')) . '</h3>';
        echo self :: get_bar($quotamanager->get_used_database_space_percent(), $quotamanager->get_used_database_space() . ' / ' . $quotamanager->get_max_database_space());
        echo '<div style="clear: both;">&nbsp;</div>';
        echo $this->get_version_quota();

        $this->display_footer();
    }

    private function display_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('EditUser'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_UPDATE_USER, UserManager :: PARAM_USER_USER_ID => $this->selected_user->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ChangeQuota'), Theme :: get_common_image_path() . 'action_statistics.png', $this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_USER_QUOTA, UserManager :: PARAM_USER_USER_ID => $this->selected_user->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        echo $action_bar->as_html();
    }

    /**
     * Build a bar-view of the used quota.
     * @param float $percent The percentage of the bar that is in use
     * @param string $status A status message which will be displayed below the
     * bar.
     * @return string HTML representation of the requested bar.
     */
    private static function get_bar($percent, $status)
    {
        $html = '<div class="usage_information">';
        $html .= '<div class="usage_bar">';
        for($i = 0; $i < 100; $i ++)
        {
            if ($percent > $i)
            {
                if ($i >= 90)
                {
                    $class = 'very_critical';
                }
                elseif ($i >= 80)
                {
                    $class = 'critical';
                }
                else
                {
                    $class = 'used';
                }
            }
            else
            {
                $class = '';
            }
            $html .= '<div class="' . $class . '"></div>';
        }
        $html .= '</div>';
        $html .= '<div class="usage_status">' . $status . ' &ndash; ' . round($percent, 2) . ' %</div>';
        $html .= '</div>';
        return $html;
    }

    static function type_to_class($type)
    {
        return ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $type));
    }

    function get_version_quota()
    {
        $user = $this->selected_user;
        $user_version_quota = $user->get_version_quota();
        $html = array();

        $html[] = '<h3>' . htmlentities(Translation :: get('VersionQuota')) . '</h3>';

        $table = new SortableTable('version_quota', array($this, 'get_registered_types_count'), array($this, 'get_registered_types_data'), 1, 30, SORT_ASC);
        $table->set_additional_parameters($this->get_parameters());
        $table->set_header(0, null, false);
        $table->set_header(1, Translation :: get('Type'), false);
        $table->set_header(2, Translation :: get('Quota'), false);
        $this->table = $table;
        $html[] = $table->as_html();

        return implode("\n", $html);
    }

    private $table;

    function get_registered_types_count()
    {
        return count($this->get_registered_types()) + 1;
    }

    function get_registered_types()
    {
        return RepositoryDataManager :: get_registered_types();
    }

    function get_registered_types_data()
    {
        $pager = $this->table->get_pager();
        $current_page = $pager->_currentPage;
        $items_per_page = $pager->_perPage;

        $start = ($current_page - 1) * $items_per_page;
        $stop = $start + $items_per_page;
        //dump($start);


        $user = $this->selected_user;
        $user_version_quota = $user->get_version_quota();
        $types = $this->get_registered_types();
        $quota_data = array();

        $counter = - 1;

        if ($start == 0)
        {
            $quota_data_row = array();
            $quota_data_row[] = '<img src="' . Theme :: get_common_image_path() . 'place_versions.png" alt=""/>';
            $quota_data_row[] = Translation :: get('Default');
            $quota_data_row[] = $user->get_version_quota();
            $quota_data[] = $quota_data_row;
            $counter ++;
        }

        foreach ($types as $type)
        {
            $counter ++;

            if ($counter < $start || $counter >= $stop)
                continue;

            $quota_data_row = array();

            $quota_data_row[] = '<img src="' . Theme :: get_common_image_path() . 'content_object/' . $type . '.png" alt="' . $type . '"/>';
            $quota_data_row[] = Translation :: get(self :: type_to_class($type) . 'TypeName');
            $object = ContentObject :: factory($type);
            if ($object instanceof Versionable)
            {
                $quota_data_row[] = $user->get_version_type_quota($type);
            }
            else
            {
                $quota_data_row[] = Translation :: get('NotVersionable');
            }

            $quota_data[] = $quota_data_row;

        }
        return $quota_data;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserManagerAdminUserBrowserComponent')));
    	$breadcrumbtrail->add_help('user_quota_viewer');
    }
    
    function get_additional_parameters()
    {
    	return array(UserManager :: PARAM_USER_USER_ID);
    }
}
?>
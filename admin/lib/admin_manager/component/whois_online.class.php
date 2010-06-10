<?php
/**
 * $Id: whois_online.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */
require_once dirname(__FILE__) . '/../../../trackers/online_tracker.class.php';

/**
 * Component to view whois online
 */
class AdminManagerWhoisOnlineComponent extends AdminManager
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('WhoisOnline')));
        $trail->add_help('common whoisonline');

        $world = PlatformSetting :: get('whoisonlineaccess');

        if ($world == "1" || ($this->get_user_id() && $world == "2"))
        {
            $user_id = Request :: get('uid');
            if (isset($user_id))
            {
                $output = $this->get_user_html($user_id);
                $trail->add(new Breadcrumb($this->get_url(array('uid' => $user_id)), Translation :: get('UserDetail')));
            }
            else
            {
                $output = $this->get_table_html();
            }
            $this->display_header();
            echo $output;
            $this->display_footer();
        }
        else
        {
            $this->display_header();
            $this->display_error_message('NotAllowed');
            $this->display_footer();
        }

    }

    private function get_table_html()
    {
        $parameters = $this->get_parameters(true);

        $table = new WhoisOnlineTable($this, $parameters, $this->get_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode("\n", $html);
    }

    function get_condition()
    {
        $tracking = new OnlineTracker();
        $items = $tracking->retrieve_tracker_items();
        foreach ($items as $item)
            $users[] = $item->get_user_id();

        if ($users)
            return new InCondition(User :: PROPERTY_ID, $users);
        else
            return new EqualityCondition(User :: PROPERTY_ID, - 1);
    }

    private function get_user_html($user_id)
    {
        $user = UserDataManager :: get_instance()->retrieve_user($user_id);

        $html[] = '<br /><div style="float: left; width: 150px;">';
        $html[] = Translation :: get('Username') . ':<br />';
        $html[] = Translation :: get('Fullname') . ':<br />';
        $html[] = Translation :: get('OfficialCode') . ':<br />';
        $html[] = Translation :: get('Email') . ':<br />';
        $html[] = Translation :: get('Status') . ':<br />';
        $html[] = '</div><div style="float: left; width: 250px;">';
        $html[] = $user->get_username() . '<br />';
        $html[] = $user->get_fullname() . '<br />';
        $html[] = $user->get_official_code() . '<br />';
        $html[] = $user->get_email() . '<br />';
        $html[] = $user->get_status_name() . '<br />';
        $html[] = '</div><div style="float: right; max-width: 400px;">';
        $html[] = '<img src="' . $user->get_full_picture_url() . '" />';
        $html[] = '</div>';

        return implode("\n", $html);
    }

}
?>
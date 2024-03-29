<?php

namespace admin;

use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\PlatformSetting;
use tracking\Tracker;
use common\libraries\InCondition;
use user\UserDataManager;
use user\User;

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
        $world = PlatformSetting :: get('whoisonlineaccess');

        if ($world == "1" || ($this->get_user_id() && $world == "2"))
        {
            $user_id = Request :: get('uid');
            if (isset($user_id))
            {
                $output = $this->get_user_html($user_id);
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
        $users = array();
        $items = Tracker :: get_data(OnlineTracker :: CLASS_NAME, AdminManager :: APPLICATION_NAME);
        while ($item = $items->next_result())
        {
            $users[] = $item->get_user_id();
        }

        if (!empty($users))
        {
            return new InCondition(User :: PROPERTY_ID, $users);
        }
        else
        {
            return new EqualityCondition(User :: PROPERTY_ID, - 1);
        }
    }

    private function get_user_html($user_id)
    {
        $user = UserDataManager :: get_instance()->retrieve_user($user_id);

        $html[] = '<br /><div style="float: left; width: 150px;">';
        $html[] = Translation :: get('Username', array(), 'user') . ':<br />';
        $html[] = Translation :: get('Fullname', array(), 'user') . ':<br />';
        $html[] = Translation :: get('OfficialCode', array(), 'user') . ':<br />';
        $html[] = Translation :: get('Email', array(), 'user') . ':<br />';
        $html[] = Translation :: get('Status', array(), 'user') . ':<br />';
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

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('admin_whois_online');
    }

}

?>
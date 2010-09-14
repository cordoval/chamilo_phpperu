<?php
/**
 * $Id: chat_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.chat.component
 */
require_once dirname(__FILE__) . '/../chat_tool.class.php';
require_once Path :: get_plugin_path() . '/phpfreechat/src/phpfreechat.class.php';

class ChatToolViewerComponent extends ChatTool
{

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $course = $this->get_course();
        $user = $this->get_user();
        $course_rel_user = WeblcmsDataManager :: get_instance()->retrieve_course_user_relation($course->get_id(), $user->get_id());
        
        $params = array();
        
        if (($course_rel_user && $course_rel_user->get_status() == 1) || $user->is_platform_admin())
            $params["isadmin"] = true;
        
        $params["data_public_url"] = Path :: get(WEB_PATH) . 'plugin/phpfreechat/data/public';
        $params["data_public_path"] = Path :: get(SYS_PATH) . 'plugin/phpfreechat/data/public';
        $params["server_script_url"] = $_SERVER['REQUEST_URI'];
        $params["serverid"] = $course->get_id();
        $params["title"] = $course->get_name();
        $params["nick"] = $user->get_username();
        $params["frozen_nick"] = true;
        $params["channels"] = array($course->get_name());
        $params["max_channels"] = 1;
        $params["theme"] = "blune";
        $params["display_pfc_logo"] = false;
        $params["display_ping"] = false;
        $params["displaytabclosebutton"] = false;
        $params["btn_sh_whosonline"] = false;
        $params["btn_sh_smileys"] = false;
        $params["displaytabimage"] = false;
        
        $chat = new phpFreeChat($params);
        
        $trail = BreadcrumbTrail :: get_instance();
        
        
        $this->display_header();
        if (! function_exists('filemtime'))
            echo Translation :: get('FileMTimeWarning');
        
        $chat->printChat();
        $this->display_footer();
    }

    function  add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('BlogToolBrowserComponent')));
        $trail->add_help('weblcms_tool_chat_viewer');
    }
    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }

}
?>
<?php
/**
 * $Id: help_manager.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.help_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
class HelpManager extends CoreApplication
{
    const APPLICATION_NAME = 'help';

    const PARAM_HELP_ITEM = 'help_item';

    const ACTION_UPDATE_HELP_ITEM = 'updater';
    const ACTION_RIGHT_EDITS = 'rights_editor';
    const ACTION_BROWSE_HELP_ITEMS = 'browser';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_HELP_ITEMS;

    function HelpManager($user = null)
    {
        parent :: __construct($user);
    }

    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('List'), Translation :: get('ListDescription'), Theme :: get_image_path() . 'browse_list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_HELP_ITEMS), array(), false, Redirect :: TYPE_CORE));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;

        return $info;
    }

    public function count_help_items($condition)
    {
        return HelpDataManager :: get_instance()->count_help_items($condition);
    }

    public function retrieve_help_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return HelpDataManager :: get_instance()->retrieve_help_items($condition, $offset, $count, $order_property);
    }

    public function retrieve_help_item($name, $language)
    {
        return HelpDataManager :: get_instance()->retrieve_help_item($name, $language);
    }

    public static function get_help_url($name)
    {
        $help_item = self :: get_help_item_by_name($name);
        if ($help_item)
            return '<a class="help" href="' . $help_item->get_url() . '" target="about:blank">' . Translation :: get('Help') . '</a>';
    }

    public static function get_tool_bar_help_item($name)
    {
        $help_item = self :: get_help_item_by_name($name);
        if ($help_item)
        {

            return new ToolbarItem(Translation :: get('Help'), Theme :: get_common_image_path() . 'action_help.png', $help_item ? $help_item->get_url() : '', ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'help', 'about:blank');
        }
        else
        {
            return false;
        }
    }

    private static function get_help_item_by_name($name)
    {
        $user_id = Session :: get_user_id();
        $user = UserDataManager :: get_instance()->retrieve_user($user_id);

        $language = LocalSetting :: get('platform_language');

        $help_item = HelpDataManager :: get_instance()->retrieve_help_item_by_name_and_language($name, $language);
        return $help_item;
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
}
?>
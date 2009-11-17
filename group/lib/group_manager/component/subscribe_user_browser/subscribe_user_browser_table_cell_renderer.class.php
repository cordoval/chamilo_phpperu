<?php
/**
 * $Id: subscribe_user_browser_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.group_manager.component.subscribe_user_browser
 */
require_once dirname(__FILE__) . '/subscribe_user_browser_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscribeUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
    /**
     * The weblcms browser component
     */
    private $browser;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function SubscribeUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === SubscribeUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case User :: PROPERTY_STATUS :
                $course_user_relation = $this->browser->get_parent()->retrieve_course_user_relation($this->browser->get_course_id(), $user->get_id());
                if ($course_user_relation->get_status() == 1)
                {
                    return Translation :: get('CourseAdmin');
                }
                else
                {
                    return Translation :: get('Student');
                }
            case User :: PROPERTY_PLATFORMADMIN :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('PlatformAdmin');
                }
                else
                {
                    return '';
                }
            case User :: PROPERTY_EMAIL :
                return '<a href="mailto:' . $user->get_email() . '">' . $user->get_email() . '</a>';
        }
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     * @param User $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($user)
    {
        $group = $this->browser->get_group();
        $toolbar_data = array();
        
        $subscribe_url = $this->browser->get_group_rel_user_subscribing_url($group, $user);
        $toolbar_data[] = array('href' => $subscribe_url, 'label' => Translation :: get('Subscribe'), 'img' => Theme :: get_common_image_path() . 'action_subscribe.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
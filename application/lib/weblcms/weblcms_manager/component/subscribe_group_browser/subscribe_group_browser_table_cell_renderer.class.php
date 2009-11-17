<?php
/**
 * $Id: subscribe_group_browser_table_cell_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.subscribe_group_browser
 */
require_once dirname(__FILE__) . '/subscribe_group_browser_table_column_model.class.php';
require_once Path :: get_group_path() . '/lib/group_table/default_group_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SubscribeGroupBrowserTableCellRenderer extends DefaultGroupTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function SubscribeGroupBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $group)
    {
        if ($column === SubscribeGroupBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($group);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case Group :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $group);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                return $title_short;
            //return '<a href="'.htmlentities($this->browser->get_group_viewing_url($group)).'" title="'.$title.'">'.$title_short.'</a>';
            case Group :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $group));
                //				if(strlen($description) > 175)
                //				{
                //					$description = mb_substr($description,0,170).'&hellip;';
                //				}
                return Utilities :: truncate_string($description, 175);
            case Translation :: get('Users') :
                $condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
                $count = GroupDataManager :: get_instance()->count_group_rel_users($condition);
                return $count;
            case Translation :: get('Subgroups') :
                $condition = new EqualityCondition(Group :: PROPERTY_PARENT, $group->get_id());
                $count = GroupDataManager :: get_instance()->count_groups($condition);
                return $count;
        }
        
        return parent :: render_cell($column, $group);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($group)
    {
        $toolbar_data = array();
        
        $parameters[Application :: PARAM_ACTION] = WeblcmsManager :: ACTION_SUBSCRIBE_GROUPS;
        $parameters[WeblcmsManager :: PARAM_USERS] = $group->get_id();
        
        $toolbar_data[] = array('href' => $this->browser->get_url($parameters), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_subscribe.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
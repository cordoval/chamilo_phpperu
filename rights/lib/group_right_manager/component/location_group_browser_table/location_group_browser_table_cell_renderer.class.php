<?php
/**
 * $Id: location_group_browser_table_cell_renderer.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.group_right_manager.component.location_group_browser_table
 */
require_once dirname(__FILE__) . '/location_group_browser_table_column_model.class.php';
require_once Path :: get_group_path() . 'lib/group_table/default_group_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LocationGroupBrowserTableCellRenderer extends DefaultGroupTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LocationGroupBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $group)
    {
        if ($column === LocationGroupBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($group);
        }
        
        if (LocationGroupBrowserTableColumnModel :: is_rights_column($column))
        {
            return $this->get_rights_column_value($column, $group);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            //			case Group :: PROPERTY_NAME :
            //				$title = parent :: render_cell($column, $group);
            //				$title_short = $title;
            //				if(strlen($title_short) > 53)
            //				{
            //					$title_short = mb_substr($title_short,0,50).'&hellip;';
            //				}
            //				return '<a href="'.htmlentities($this->browser->get_group_viewing_url($group)).'" title="'.$title.'">'.$title_short.'</a>';
            case Group :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $group));
                //				if(strlen($description) > 175)
                //				{
                //					$description = mb_substr($description,0,170).'&hellip;';
                //				}
                return Utilities :: truncate_string($description);
            case Translation :: get('Users') :
                return $group->count_users();
            case Translation :: get('Subgroups') :
                return $group->count_subgroups(true);
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
  
    }

    private function get_rights_column_value($column, $group)
    {
        $browser = $this->browser;
        $location = $browser->get_location();
        $locked_parent = $location->get_locked_parent();
        $rights = RightsUtilities :: get_available_rights($this->browser->get_source());
        $group_id = $group->get_id();
        
        $location_url = $browser->get_url(array('application' => $this->application, 'location' => ($locked_parent ? $locked_parent->get_id() : $location->get_id())));
        
        foreach ($rights as $right_name => $right_id)
        {
            $column_name = Translation :: get(Utilities :: underscores_to_camelcase(strtolower($right_name)));
            if ($column->get_name() == $column_name)
            {
                $rights_url = $browser->get_url(array(GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager :: ACTION_SET_GROUP_RIGHTS, 'group_id' => $group_id, 'right_id' => $right_id, GroupRightManager :: PARAM_LOCATION => $location->get_id()));
                return RightsUtilities :: get_rights_icon($location_url, $rights_url, $locked_parent, $right_id, $group, $location);
            }
        }
        return '&nbsp;';
    }
}
?>
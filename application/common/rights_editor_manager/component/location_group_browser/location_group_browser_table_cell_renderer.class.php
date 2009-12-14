<?php
/**
 * $Id: location_group_browser_table_cell_renderer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component.location_group_bowser
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
        $toolbar_data = array();
        
        //		$toolbar_data[] = array(
        //			'href' => $this->browser->get_group_editing_url($group),
        //			'label' => Translation :: get('Edit'),
        //			'img' => Theme :: get_common_image_path().'action_edit.png'
        //		);
        //
        //		$toolbar_data[] = array(
        //			'href' => $this->browser->get_group_suscribe_user_browser_url($group),
        //			'label' => Translation :: get('AddUsers'),
        //			'img' => Theme :: get_common_image_path().'action_subscribe.png',
        //		);
        //
        //		$condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
        //		$users = $this->browser->retrieve_group_rel_users($condition);
        //		$visible = ($users->size() > 0);
        //
        //		if($visible)
        //		{
        //			$toolbar_data[] = array(
        //				'href' => $this->browser->get_group_emptying_url($group),
        //				'label' => Translation :: get('Truncate'),
        //				'img' => Theme :: get_common_image_path().'action_recycle_bin.png',
        //			);
        //		}
        //		else
        //		{
        //			$toolbar_data[] = array(
        //				'label' => Translation :: get('TruncateNA'),
        //				'img' => Theme :: get_common_image_path().'action_recycle_bin_na.png',
        //			);
        //		}
        //
        //		$toolbar_data[] = array(
        //			'href' => $this->browser->get_group_delete_url($group),
        //			'label' => Translation :: get('Delete'),
        //			'img' => Theme :: get_common_image_path().'action_delete.png'
        //		);
        //
        //		$toolbar_data[] = array(
        //			'href' => $this->browser->get_move_group_url($group),
        //			'label' => Translation :: get('Move'),
        //			'img' => Theme :: get_common_image_path().'action_move.png'
        //		);
        //
        //		$toolbar_data[] = array(
        //			'href' => $this->browser->get_manage_group_rights_url($group),
        //			'label' => Translation :: get('ManageRightsTemplates'),
        //			'img' => Theme :: get_common_image_path().'action_rights.png'
        //		);
        

        return Utilities :: build_toolbar($toolbar_data);
    }

    private function get_rights_column_value($column, $group)
    {
        $browser = $this->browser;
        $locations = $browser->get_locations();
        $locked_parent = $locations[0]->get_locked_parent();
        $rights = $this->browser->get_available_rights();
        $group_id = $group->get_id();
        
        $location_url = $browser->get_url(array('application' => $this->application, 'location' => ($locked_parent ? $locked_parent->get_id() : $locations[0]->get_id())));
        
        foreach ($rights as $right_name => $right_id)
        {
            $column_name = Translation :: get(Utilities :: underscores_to_camelcase(strtolower($right_name)));
            if ($column->get_name() == $column_name)
            {
                $rights_url = $browser->get_url(array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_SET_GROUP_RIGHTS, 'group_id' => $group_id, 'right_id' => $right_id));
                return RightsUtilities :: get_rights_icon($location_url, $rights_url, $locked_parent, $right_id, $group, $locations[0]);
            }
        }
        return '&nbsp;';
    }
}
?>
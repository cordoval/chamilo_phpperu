<?php
/**
 * $Id: laika_group_browser_table_cell_renderer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.laika_group_browser
 */
require_once dirname(__FILE__) . '/laika_group_browser_table_column_model.class.php';
require_once Path :: get_group_path() . '/lib/group_table/default_group_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../laika_manager.class.php';
require_once dirname(__FILE__) . '/../../../laika_attempt.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LaikaGroupBrowserTableCellRenderer extends DefaultGroupTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LaikaGroupBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $group)
    {
        if ($column === LaikaGroupBrowserTableColumnModel :: get_modification_column())
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
                return '<a href="' . htmlentities($this->browser->get_group_statistics_viewing_url($group)) . '" title="' . $title . '">' . $title_short . '</a>';
            case Group :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string(parent :: render_cell($column, $group), 170);
            case Translation :: get('Users') :
                return $group->count_users(true, true);
            case Translation :: get('Subgroups') :
                return $group->count_subgroups(true);
            case Translation :: get('Participants') :
                return $this->count_participants($group);
            case Translation :: get('Percentage') :
                $users = $group->count_users(true, true);
                $participants = $this->count_participants($group);
                $percentage = ($participants / $users) * 100;
                
                if ($percentage != 0)
                {
                    return number_format($percentage, 2);
                }
                else
                {
                    return null;
                }
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
        $toolbar= new Toolbar();
        

        return $toolbar->as_html();
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
        //			'href' => $this->browser->get_manage_roles_url($group),
        //			'label' => Translation :: get('ManageRoles'),
        //			'img' => Theme :: get_common_image_path().'action_rights.png'
        //		);
        

    }

    function count_participants($group)
    {
        $group_users = $group->get_users(true, true);
        
        if (count($group_users) > 0)
        {
            $condition = new InCondition(LaikaAttempt :: PROPERTY_USER_ID, $group_users);
            return $this->browser->count_laika_users($condition);
        }
        else
        {
            return 0;
        }
    }
}
?>
<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: exporter.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerExporterComponent extends GroupManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_EXPORT, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $form = new GroupExportForm(GroupExportForm :: TYPE_EXPORT, $this->get_url());
        
        if ($form->validate())
        {
            $export = $form->exportValues();
            $file_type = $export['file_type'];
            $data['groups'] = $this->build_group_tree(0);
            $this->export_groups($file_type, $data);
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    function build_group_tree($parent_group)
    {
        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, $parent_group);
        $result = $this->retrieve_groups($condition);
        while ($group = $result->next_result())
        {
            $group_array[Group :: PROPERTY_NAME] = htmlspecialchars($group->get_name());
            $group_array[Group :: PROPERTY_DESCRIPTION] = htmlspecialchars($group->get_description());
            $group_array['children'] = $this->build_group_tree($group->get_id());
            $data[] = $group_array;
        }
        
        return $data;
    }

    function export_groups($file_type, $data)
    {       
        $filename = 'export_groups_' . date('Y-m-d_H-i-s');
    	if ($file_type == 'pdf')
        {
            $data = array(array('key' => 'groups', 'data' => $data));
        }
        $export = Export :: factory($file_type, $data);
        $export->set_filename($filename);
        $export->send_to_browser();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('group general');
    }
}
?>
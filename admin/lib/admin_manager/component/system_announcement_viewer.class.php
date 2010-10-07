<?php
/**
 * $Id: system_announcement_viewer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

class AdminManagerSystemAnnouncementViewerComponent extends AdminManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID);
        
        $user = $this->get_user();
        
        if ($id)
        {
            $system_announcement_publication = $this->retrieve_system_announcement_publication($id);
            $object = $system_announcement_publication->get_publication_object();
            
            $display = ContentObjectDisplay :: factory($object);
            
            $this->display_header();
            echo $display->get_full_html();
            echo $this->get_toolbar($system_announcement_publication, $object);
            $this->display_footer();
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSystemAnnouncementSelected')));
        }
    }

    function get_toolbar($system_announcement_publication, $object)
    {
        $user = $this->get_user();
        $toolbar = new Toolbar();
        
        if ($user->is_platform_admin())
        {
            
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_system_announcement_publication_editing_url($system_announcement_publication), ToolbarItem :: DISPLAY_ICON));
            
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_system_announcement_publication_deleting_url($system_announcement_publication), ToolbarItem :: DISPLAY_ICON, true));
        
        }
        
        return $toolbar->as_html();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS)), Translation :: get('AdminManagerSystemAnnouncementBrowserComponent')));
    	$breadcrumbtrail->add_help('admin_system_announcements_viewer');
    }
    
    function get_additional_parameters()
    {
    	return array(AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID);
    }
}
?>
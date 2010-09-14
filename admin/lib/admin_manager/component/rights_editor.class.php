<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Rights Editor for admin application
 *
 * @author Pieterjan Broekaert
 */
class AdminManagerRightsEditorComponent extends AdminManager implements AdministrationComponent, DelegateComponent
{
    function run()
    {
        $location[] = AdminRights::get_location_by_identifier(AdminManager::APPLICATION_NAME, AdminRights::TYPE_ADMIN_COMPONENT, AdminRights::LOCATION_SYSTEM_ANNOUNCEMENTS);
        
        $manager = new RightsEditorManager($this, $location);
        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
    }

    function get_available_rights()
    {
        return AdminRights :: get_available_rights();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS)), Translation :: get('AdminManagerSystemAnnouncementBrowserComponent')));
    	$breadcrumbtrail->add_help('admin_rights_editor');
    }
    
    function get_additional_parameters()
    {
    	return array(AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID);
    }
    
}
?>

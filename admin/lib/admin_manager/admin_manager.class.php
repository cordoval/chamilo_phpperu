<?php
/**
 * $Id: admin_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package admin.lib.admin_manager
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */


/**
 * The admin allows the platform admin to configure certain aspects of his platform
 */
class AdminManager extends CoreApplication
{
    const APPLICATION_NAME = 'admin';
    
    const PARAM_WEB_APPLICATION = 'web_application';
    const PARAM_SYSTEM_ANNOUNCEMENT_ID = 'announcement';
    
    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_EDIT_SELECTED = 'edit_selected';
    
    const ACTION_ADMIN_BROWSER = 'browse';
    const ACTION_LANGUAGES = 'languages';
    const ACTION_CONFIGURE_PLATFORM = 'configure';
    const ACTION_MANAGE_PACKAGES = 'package';
    const ACTION_CREATE_SYSTEM_ANNOUNCEMENT = 'sysannouncer';
    const ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS = 'sysbrowser';
    const ACTION_EDIT_SYSTEM_ANNOUNCEMENT = 'syseditor';
    const ACTION_DELETE_SYSTEM_ANNOUNCEMENT = 'sysdeleter';
    const ACTION_VIEW_SYSTEM_ANNOUNCEMENT = 'sysviewer';
    const ACTION_HIDE_SYSTEM_ANNOUNCEMENT = 'sysvisibility';
    const ACTION_MANAGE_CATEGORIES = 'manage_categories';
    const ACTION_WHOIS_ONLINE = 'whois_online';
    const ACTION_DIAGNOSE = 'diagnose';
    const ACTION_VIEW_LOGS = 'view_logs';
    const ACTION_IMPORTER = 'importer';

    /**
     * Constructor
     * @param User $user The current user
     */
    function AdminManager($user = null)
    {
        parent :: __construct($user);
    }

    /**
     * Run this admin manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_CONFIGURE_PLATFORM :
                $component = AdminManagerComponent :: factory('Configurer', $this);
                break;
            case self :: ACTION_CREATE_SYSTEM_ANNOUNCEMENT :
                $component = AdminManagerComponent :: factory('SystemAnnouncementCreator', $this);
                break;
            case self :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS :
                $component = AdminManagerComponent :: factory('SystemAnnouncementBrowser', $this);
                break;
            case self :: ACTION_EDIT_SYSTEM_ANNOUNCEMENT :
                $component = AdminManagerComponent :: factory('SystemAnnouncementEditor', $this);
                break;
            case self :: ACTION_DELETE_SYSTEM_ANNOUNCEMENT :
                $component = AdminManagerComponent :: factory('SystemAnnouncementDeleter', $this);
                break;
            case self :: ACTION_VIEW_SYSTEM_ANNOUNCEMENT :
                $component = AdminManagerComponent :: factory('SystemAnnouncementViewer', $this);
                break;
            case self :: ACTION_HIDE_SYSTEM_ANNOUNCEMENT :
                $component = AdminManagerComponent :: factory('SystemAnnouncementHider', $this);
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
                $component = AdminManagerComponent :: factory('CategoryManager', $this);
                break;
            case self :: ACTION_WHOIS_ONLINE :
                $component = AdminManagerComponent :: factory('WhoisOnline', $this);
                break;
            case self :: ACTION_DIAGNOSE :
                $component = AdminManagerComponent :: factory('Diagnoser', $this);
                break;
            case self :: ACTION_MANAGE_PACKAGES :
                $component = AdminManagerComponent :: factory('Packager', $this);
                break;
            case self :: ACTION_VIEW_LOGS :
                $component = AdminManagerComponent :: factory('LogViewer', $this);
                break;
            case self :: ACTION_IMPORTER :
                $component = AdminManagerComponent :: factory('Importer', $this);
                break;
            default :
                $component = AdminManagerComponent :: factory('Browser', $this);
        }
        $component->run();
    }

    /**
     * Displays the header.
     * @param array $breadcrumbs Breadcrumbs to show in the header.
     * @param boolean $display_search Should the header include a search form or
     * not?
     */
    function display_header($breadcrumbtrail = array (), $display_search = false, $helpitem)
    {
        if (is_null($breadcrumbtrail))
        {
            $breadcrumbtrail = new BreadcrumbTrail();
        }
        
        $title = $breadcrumbtrail->get_last()->get_name();
        $title_short = $title;
        if (strlen($title_short) > 53)
        {
            $title_short = substr($title_short, 0, 50) . '&hellip;';
        }
        Display :: header($breadcrumbtrail, $helpitem);
        echo '<h3 style="float: left;" title="' . $title . '">' . $title_short . '</h3>';
        if ($display_search)
        {
            //$this->display_search_form();
        }
        echo '<div class="clear">&nbsp;</div>';
        
        $message = Request :: get(Application :: PARAM_MESSAGE);
        if (isset($message))
        {
            $this->display_message($message);
        }
        $message = Request :: get(Application :: PARAM_ERROR_MESSAGE);
        if (isset($message))
        {
            $this->display_error_message($message);
        }
    }

    /**
     * Displays the footer.
     */
    function display_footer()
    {
        echo '<div class="clear">&nbsp;</div>';
        Display :: footer();
    }

    function get_application_platform_admin_links()
    {
        $info = array();
        $user = $this->get_user();
        
        // 1. Admin-core components
        $links = array();
        $links[] = array('name' => Translation :: get('Settings'), 'description' => Translation :: get('SettingsDescription'), 'action' => 'manage', 'url' => $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PLATFORM)));
        $links[] = array('name' => Translation :: get('Importer'), 'description' => Translation :: get('ImporterDescription'), 'action' => 'import', 'url' => $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORTER)));
        $links[] = array('name' => Translation :: get('ManagePackages'), 'description' => Translation :: get('ManagePackagesDescription'), 'action' => 'build', 'url' => $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_PACKAGES)));
        $links[] = array('name' => Translation :: get('SystemAnnouncements'), 'description' => Translation :: get('SystemAnnouncementsDescription'), 'action' => 'list', 'url' => $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS)));
        $links[] = array('name' => Translation :: get('ManageCategories'), 'description' => Translation :: get('ManageCategoriesDescription'), 'action' => 'list', 'url' => $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES)));
        $links[] = array('name' => Translation :: get('Diagnose'), 'description' => Translation :: get('DiagnoseDescription'), 'action' => 'information', 'url' => $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DIAGNOSE)));
        $links[] = array('name' => Translation :: get('LogsViewer'), 'description' => Translation :: get('LogsViewerDescription'), 'action' => 'information', 'url' => $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_LOGS)));
        
        $admin_info = parent :: get_application_platform_admin_links();
        $admin_info['links'] = $links;
        $info[] = $admin_info;
        
        // 2. Repository
        $repository_manager = new RepositoryManager($user);
        $info[] = $repository_manager->get_application_platform_admin_links();
        
        // 3. UserManager
        $user_manager = new UserManager($user->get_id());
        $info[] = $user_manager->get_application_platform_admin_links();
        
        // 4. Roles'n'Rights
        $rights_manager = new RightsManager($user->get_id());
        $info[] = $rights_manager->get_application_platform_admin_links();
        
        // 5. Groups
        $group_manager = new GroupManager($user->get_id());
        $info[] = $group_manager->get_application_platform_admin_links();
        
        // 6. Webservices
        $webservice_manager = new WebserviceManager($user->get_id());
        $info[] = $webservice_manager->get_application_platform_admin_links();
        
        // 7. Tracking
        $tracking_manager = new TrackingManager($user);
        $info[] = $tracking_manager->get_application_platform_admin_links();
        
        // 8. Reporting
        $reporting_manager = new ReportingManager($user);
        $info[] = $reporting_manager->get_application_platform_admin_links();
        
        // 9. Home
        $home_manager = new HomeManager($user->get_id());
        $info[] = $home_manager->get_application_platform_admin_links();
        
        // 10. Menu
        $menu_manager = new MenuManager($user->get_id());
        $info[] = $menu_manager->get_application_platform_admin_links();
        
        // 11. Migration
        /*$migration_manager = new MigrationManager($user->get_id());
        $info[] = $migration_manager->get_application_platform_admin_links();*/
        
        $help_manager = new HelpManager($user->get_id());
        $info[] = $help_manager->get_application_platform_admin_links();
        
        // 12.The links for the plugin applications running on top of the essential Chamilo components
        $applications = WebApplication :: load_all();
        foreach ($applications as $index => $application_name)
        {
            $application = Application :: factory($application_name);
            $info[] = $application->get_application_platform_admin_links();
        }
        
        return $info;
    }
    
	function get_application_platform_import_links()
	{
        $user = $this->get_user();
        
        // 1. Admin-core components
        $links = array();
        
        // 2. Repository
        $repository_manager = new RepositoryManager($user);
        $links = array_merge($links, $repository_manager->get_application_platform_import_links());
        
        // 3. UserManager
        $user_manager = new UserManager($user->get_id());
        $links = array_merge($links, $user_manager->get_application_platform_import_links());
        
        // 4. Roles'n'Rights
        $rights_manager = new RightsManager($user->get_id());
        $links = array_merge($links, $rights_manager->get_application_platform_import_links());
        
        // 5. Groups
        $group_manager = new GroupManager($user->get_id());
        $links = array_merge($links, $group_manager->get_application_platform_import_links());
        
        // 6. Webservices
        $webservice_manager = new WebserviceManager($user->get_id());
        $links = array_merge($links, $webservice_manager->get_application_platform_import_links());
        
        // 7. Tracking
        $tracking_manager = new TrackingManager($user);
        $links = array_merge($links, $tracking_manager->get_application_platform_import_links());
        
        // 8. Reporting
        $reporting_manager = new ReportingManager($user);
        $links = array_merge($links, $reporting_manager->get_application_platform_import_links());
        
        // 9. Home
        $home_manager = new HomeManager($user->get_id());
        $links = array_merge($links, $home_manager->get_application_platform_import_links());
        
        // 10. Menu
        $menu_manager = new MenuManager($user->get_id());
        $links = array_merge($links, $menu_manager->get_application_platform_import_links());
        
        // 11. Migration
        /*$migration_manager = new MigrationManager($user->get_id());
        $links[] = $migration_manager->get_application_platform_admin_links();*/
        
        $help_manager = new HelpManager($user->get_id());
        $links = array_merge($links, $help_manager->get_application_platform_import_links());
        
        // 12.The links for the plugin applications running on top of the essential Chamilo components
        $applications = WebApplication :: load_all();
        foreach ($applications as $index => $application_name)
        {
            $application = Application :: factory($application_name);
            $links = array_merge($links, $application->get_application_platform_import_links());
        }
        
        return $links;
    }

    /**
     * Count the system announcements
     * @param Condition $condition
     * @return int
     */
    function count_system_announcement_publications($condition = null)
    {
        $pmdm = AdminDataManager :: get_instance();
        return $pmdm->count_system_announcement_publications($condition);
    }

    function count_registrations($condition = null)
    {
        $pmdm = AdminDataManager :: get_instance();
        return $pmdm->count_registrations($condition);
    }

    function count_remote_packages($condition = null)
    {
        $pmdm = AdminDataManager :: get_instance();
        return $pmdm->count_remote_packages($condition);
    }

    /**
     * Retrieve a system announcement
     * @param int $id
     * @return SystemAnnouncementPublication
     */
    function retrieve_system_announcement_publication($id)
    {
        $pmdm = AdminDataManager :: get_instance();
        return $pmdm->retrieve_system_announcement_publication($id);
    }

    function retrieve_remote_package($id)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->retrieve_remote_package($id);
    }

    function retrieve_remote_packages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->retrieve_remote_packages($condition, $order_by, $offset, $max_objects);
    }

    function retrieve_registration($id)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->retrieve_registration($id);
    }

    function retrieve_registrations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->retrieve_registrations($condition, $order_by, $offset, $max_objects);
    }

    /**
     * Retrieve a series of system announcements
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return SystemAnnouncementPublicationResultSet
     */
    function retrieve_system_announcement_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $pmdm = AdminDataManager :: get_instance();
        return $pmdm->retrieve_system_announcement_publications($condition, $order_by, $offset, $max_objects);
    }

    function get_system_announcement_publication_deleting_url($system_announcement_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_SYSTEM_ANNOUNCEMENT, self :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $system_announcement_publication->get_id()));
    }

    function get_system_announcement_publication_visibility_url($system_announcement_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_HIDE_SYSTEM_ANNOUNCEMENT, self :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $system_announcement_publication->get_id()));
    }

    function get_system_announcement_publication_viewing_url($system_announcement_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_SYSTEM_ANNOUNCEMENT, self :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $system_announcement_publication->get_id()));
    }

    function get_system_announcement_publication_editing_url($system_announcement_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_SYSTEM_ANNOUNCEMENT, self :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $system_announcement_publication->get_id()));
    }

    function get_system_announcement_publication_creating_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_SYSTEM_ANNOUNCEMENT));
    }

    /**
     * Renders the users block and returns it.
     */
    function render_block($block)
    {
        $admin_block = AdminBlock :: factory($this, $block);
        return $admin_block->run();
    }

    /*
	 * Inherited
	 */
    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return AdminDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    /*
	 * Inherited
	 */
    function get_content_object_publication_attribute($publication_id)
    {
        return AdminDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

    public function any_content_object_is_published($object_ids)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->any_content_object_is_published($object_ids);
    }

    public function count_publication_attributes($type = null, $condition = null)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->count_publication_attributes($type, $condition);
    }

    public function delete_content_object_publications($object_id)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->delete_content_object_publications($object_id);
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
     * Used to publish system announcements
     */
    function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array('system_announcement');
        
        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(Translation :: get('SystemAnnouncements'));
            return $locations;
        }
        
        return array();
    }

    /**
     * Used to publish system announcements
     */
    function publish_content_object($content_object, $location)
    {
        require_once dirname(__FILE__) . '/../system_announcement_publication.class.php';
        $pub = new SystemAnnouncementPublication();
        $pub->set_content_object_id($content_object->get_id());
        $pub->set_publisher($content_object->get_owner_id());
        $pub->create();
        
        return Translation :: get('PublicationCreated');
    }
    
    function add_publication_attributes_elements()
    {
    	
    }
}
?>
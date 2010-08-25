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
                $component = $this->create_component('Configurer');
                break;
            case self :: ACTION_CREATE_SYSTEM_ANNOUNCEMENT :
                $component = $this->create_component('SystemAnnouncementCreator');
                break;
            case self :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS :
                $component = $this->create_component('SystemAnnouncementBrowser');
                break;
            case self :: ACTION_EDIT_SYSTEM_ANNOUNCEMENT :
                $component = $this->create_component('SystemAnnouncementEditor');
                break;
            case self :: ACTION_DELETE_SYSTEM_ANNOUNCEMENT :
                $component = $this->create_component('SystemAnnouncementDeleter');
                break;
            case self :: ACTION_VIEW_SYSTEM_ANNOUNCEMENT :
                $component = $this->create_component('SystemAnnouncementViewer');
                break;
            case self :: ACTION_HIDE_SYSTEM_ANNOUNCEMENT :
                $component = $this->create_component('SystemAnnouncementHider');
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
                $component = $this->create_component('CategoryManager');
                break;
            case self :: ACTION_WHOIS_ONLINE :
                $component = $this->create_component('WhoisOnline');
                break;
            case self :: ACTION_DIAGNOSE :
                $component = $this->create_component('Diagnoser');
                break;
            case self :: ACTION_MANAGE_PACKAGES :
                $component = $this->create_component('Packager');
                break;
            case self :: ACTION_VIEW_LOGS :
                $component = $this->create_component('LogViewer');
                break;
            case self :: ACTION_IMPORTER :
                $component = $this->create_component('Importer');
                break;
            default :
                $component = $this->create_component('Browser');
        }
        $component->run();
    }

    /**
     * Displays the header.
     * @param array $breadcrumbs Breadcrumbs to show in the header.
     * @param boolean $display_search Should the header include a search form or
     * not?
     */
    function display_header($breadcrumbtrail = null, $display_search = false, $helpitem)
    {
        if (is_null($breadcrumbtrail))
        {
            $breadcrumbtrail = BreadcrumbTrail :: get_instance();
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

    public static function get_application_platform_admin_links()
    {
        $info = array();

        // 1. Admin-core components
        $links = array();
        $links[] = new DynamicAction(Translation :: get('Importer'), Translation :: get('ImporterDescription'), Theme :: get_image_path() . 'browse_import.png', Redirect :: get_link(self :: APPLICATION_NAME, array(self :: PARAM_ACTION => self :: ACTION_IMPORTER), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('ManagePackages'), Translation :: get('ManagePackagesDescription'), Theme :: get_image_path() . 'browse_build.png', Redirect :: get_link(self :: APPLICATION_NAME, array(self :: PARAM_ACTION => self :: ACTION_MANAGE_PACKAGES), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('SystemAnnouncements'), Translation :: get('SystemAnnouncementsDescription'), Theme :: get_image_path() . 'browse_list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('ManageCategories'), Translation :: get('ManageCategoriesDescription'), Theme :: get_image_path() . 'browse_list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(self :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('Diagnose'), Translation :: get('DiagnoseDescription'), Theme :: get_image_path() . 'browse_information.png', Redirect :: get_link(self :: APPLICATION_NAME, array(self :: PARAM_ACTION => self :: ACTION_DIAGNOSE), array(), false, Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(Translation :: get('LogsViewer'), Translation :: get('LogsViewerDescription'), Theme :: get_image_path() . 'browse_information.png', Redirect :: get_link(self :: APPLICATION_NAME, array(self :: PARAM_ACTION => self :: ACTION_VIEW_LOGS), array(), false, Redirect :: TYPE_CORE));

        $admin_info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $admin_info['links'] = $links;
        $info[] = $admin_info;

        $info[] = RepositoryManager :: get_application_platform_admin_links();
        $info[] = UserManager :: get_application_platform_admin_links();
        $info[] = RightsManager :: get_application_platform_admin_links();
        $info[] = GroupManager :: get_application_platform_admin_links();
        $info[] = WebserviceManager :: get_application_platform_admin_links();
        $info[] = TrackingManager :: get_application_platform_admin_links();
        $info[] = ReportingManager :: get_application_platform_admin_links();
        $info[] = HomeManager :: get_application_platform_admin_links();
        $info[] = MenuManager :: get_application_platform_admin_links();
        $info[] = MigrationManager :: get_application_platform_admin_links();
        $info[] = HelpManager :: get_application_platform_admin_links();

        //The links for the plugin applications running on top of the essential Chamilo components
        $applications = WebApplication :: load_all();
        foreach ($applications as $index => $application_name)
        {
            $info[] = call_user_func(array(WebApplication :: get_application_class_name($application_name), 'get_application_platform_admin_links'));
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
    static function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return AdminDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    /*
	 * Inherited
	 */
    static function get_content_object_publication_attribute($publication_id)
    {
        return AdminDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

    public static function any_content_object_is_published($object_ids)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->any_content_object_is_published($object_ids);
    }

    public static function count_publication_attributes($user, $type = null, $condition = null)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->count_publication_attributes($type, $condition);
    }

    public static function delete_content_object_publications($object_id)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->delete_content_object_publications($object_id);
    }

    public static function delete_content_object_publication($publication_id)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->delete_content_object_publication($publication_id);
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
    static function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array(SystemAnnouncement :: get_type_name());

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
    static function publish_content_object($content_object, $location)
    {
        require_once dirname(__FILE__) . '/../system_announcement_publication.class.php';
        $pub = new SystemAnnouncementPublication();
        $pub->set_content_object_id($content_object->get_id());
        $pub->set_publisher($content_object->get_owner_id());
        $pub->create();

        return Translation :: get('PublicationCreated');
    }

    static function add_publication_attributes_elements()
    {

    }
}
?>
<?php
/**
 * $Id: user_manager.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
class UserManager extends CoreApplication
{

    const APPLICATION_NAME = 'user';

    const PARAM_USER_USER_ID = 'user_id';
    const PARAM_ACTIVE = 'active';
    const PARAM_FIRSTLETTER = 'firstletter';

    const ACTION_CREATE_USER = 'create';
    const ACTION_BROWSE_USERS = 'adminbrowse';
    const ACTION_EXPORT_USERS = 'export';
    const ACTION_IMPORT_USERS = 'import';
    const ACTION_UPDATE_USER = 'update';
    const ACTION_DELETE_USER = 'delete';
    const ACTION_REGISTER_USER = 'register';
    const ACTION_REGISTER_INVITED_USER = 'invite';
    const ACTION_VIEW_ACCOUNT = 'account';
    const ACTION_EMAIL = 'email';
    const ACTION_USER_QUOTA = 'quota';
    const ACTION_RESET_PASSWORD = 'reset_password';
    const ACTION_CHANGE_USER = 'change_user';
    const ACTION_MANAGE_RIGHTS_TEMPLATES = 'manage_user_rights_templates';
    const ACTION_REPORTING = 'reporting';
    const ACTION_VIEW_QUOTA = 'view_quota';
    const ACTION_USER_DETAIL = 'user_detail';
    const ACTION_CHANGE_ACTIVATION = 'change_activation';
    const ACTION_ACTIVATE = 'activate';
    const ACTION_DEACTIVATE = 'deactivate';
    const ACTION_RESET_PASSWORD_MULTI = 'reset_pass_multi';

    const ACTION_VIEW_BUDDYLIST = 'buddy_view';
    const ACTION_CREATE_BUDDYLIST_CATEGORY = 'buddy_create_category';
    const ACTION_DELETE_BUDDYLIST_CATEGORY = 'buddy_delete_category';
    const ACTION_UPDATE_BUDDYLIST_CATEGORY = 'buddy_update_category';
    const ACTION_CREATE_BUDDYLIST_ITEM = 'buddy_create_item';
    const ACTION_DELETE_BUDDYLIST_ITEM = 'buddy_delete_item';
    const ACTION_CHANGE_BUDDYLIST_ITEM_STATUS = 'buddy_status_change';
    const ACTION_CHANGE_BUDDYLIST_ITEM_CATEGORY = 'buddy_category_change';

    const ACTION_BUILD_USER_FIELDS = 'user_field_builder';
    const ACTION_ADDITIONAL_ACCOUNT_INFORMATION = 'account_extra';
    const ACTION_USER_SETTINGS = 'user_settings';
    const ACTION_USER_APPROVAL_BROWSER = 'user_approval_browser';
    const ACTION_USER_APPROVER = 'user_approver';
    const ACTION_APPROVE_USER = 'approve_user';
    const ACTION_DENY_USER = 'deny_user';

    const PARAM_BUDDYLIST_CATEGORY = 'buddylist_category';
    const PARAM_BUDDYLIST_ITEM = 'buddylist_item';

    private $quota_url;
    private $publication_url;
    private $create_url;
    private $recycle_bin_url;

    function UserManager($user = null)
    {
        $user = $this->load_user($user);
        parent :: __construct($user);

        $this->load_user_theme();

        // Can users set their own theme and if they
        // can, do they have one set ? If so apply it
        $user = $this->get_user();

        if (is_object($user))
        {
            $user_can_set_theme = $this->get_platform_setting('allow_user_theme_selection');

            if ($user_can_set_theme)
            {
                $user_theme = LocalSetting :: get('theme');
                Theme :: set_theme($user_theme);
            }
        }
        $this->create_url = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_USER));
    }

    /**
     * Sets the current user based on the input passed on to the UserManager.
     * @param mixed $user The user.
     */
    function load_user($user)
    {
        if (isset($user))
        {
            if (is_object($user))
            {
                return $user;
            }
            else
            {
                if (! is_null($user))
                {
                    return ($this->retrieve_user($user));
                }
                else
                {
                    return null;
                }
            }
        }
    }

    /**
     * Sets the platform theme to the user's selection if allowed.
     */
    function load_user_theme()
    {
        // TODO: Add theme to userforms.
        $user = $this->get_user();

        if (is_object($user))
        {
        	$user_can_set_theme = $this->get_platform_setting('allow_user_theme_selection');

            if ($user_can_set_theme)
            {
                $user_theme = LocalSetting :: get('theme');
                Theme :: set_theme($user_theme);
            }
        }
    }

    /**
     * Run this user manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_CREATE_USER :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_REGISTER_USER :
                $component = $this->create_component('Register');
                break;
            case self :: ACTION_UPDATE_USER :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE_USER :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_IMPORT_USERS :
                //$this->force_menu_url($this->create_url, true);
                $component = $this->create_component('Importer');
                break;
            case self :: ACTION_EXPORT_USERS :
                //$this->force_menu_url($this->create_url, true);
                $component = $this->create_component('Exporter');
                break;
            case self :: ACTION_USER_QUOTA :
                $component = $this->create_component('quota');
                break;
            case self :: ACTION_BROWSE_USERS :
                $component = $this->create_component('AdminUserBrowser');
                break;
            case self :: ACTION_VIEW_ACCOUNT :
                $component = $this->create_component('Account');
                break;
            case self :: ACTION_RESET_PASSWORD :
                $component = $this->create_component('ResetPassword');
                break;
            case self :: ACTION_CHANGE_USER :
                $component = $this->create_component('ChangeUser');
                break;
            case self :: ACTION_MANAGE_RIGHTS_TEMPLATES :
                $component = $this->create_component('UserRightsTemplateManager');
                break;
            case self :: ACTION_VIEW_BUDDYLIST :
                $component = $this->create_component('BuddyListViewer');
                break;
            case self :: ACTION_CREATE_BUDDYLIST_CATEGORY :
                $component = $this->create_component('BuddyListCategoryCreator');
                break;
            case self :: ACTION_DELETE_BUDDYLIST_CATEGORY :
                $component = $this->create_component('BuddyListCategoryDeleter');
                break;
            case self :: ACTION_UPDATE_BUDDYLIST_CATEGORY :
                $component = $this->create_component('BuddyListCategoryEditor');
                break;
            case self :: ACTION_CREATE_BUDDYLIST_ITEM :
                $component = $this->create_component('BuddyListItemCreator');
                break;
            case self :: ACTION_DELETE_BUDDYLIST_ITEM :
                $component = $this->create_component('BuddyListItemDeleter');
                break;
            case self :: ACTION_CHANGE_BUDDYLIST_ITEM_STATUS :
                $component = $this->create_component('BuddyListItemStatusChanger');
                break;
            case self :: ACTION_CHANGE_BUDDYLIST_ITEM_CATEGORY :
                $component = $this->create_component('BuddyListItemCategoryChanger');
                break;
            case self :: ACTION_REPORTING :
                $component = $this->create_component('Reporting');
                break;
            case self :: ACTION_VIEW_QUOTA :
                $component = $this->create_component('QuotaViewer');
                break;
            case self :: ACTION_USER_DETAIL:
            	$component = $this->create_component('UserDetail');
                break;
            case self :: ACTION_CHANGE_ACTIVATION :
                $component = $this->create_component('ActiveChanger');
                break;
            case self :: ACTION_ACTIVATE :
                $component = $this->create_component('ActiveChanger');
                Request :: set_get(self :: PARAM_ACTIVE, 1);
                break;
            case self :: ACTION_DEACTIVATE :
                $component = $this->create_component('ActiveChanger');
                Request :: set_get(self :: PARAM_ACTIVE, 0);
                break;
            case self :: ACTION_RESET_PASSWORD_MULTI:
            	$component = $this->create_component('MultiPasswordResetter');
                break;
            case self :: ACTION_BUILD_USER_FIELDS:
            	$component = $this->create_component('UserFieldsBuilder');
                break;
            case self :: ACTION_ADDITIONAL_ACCOUNT_INFORMATION:
            	$component = $this->create_component('AdditionalAccountInformation');
                break;
            case self :: ACTION_USER_SETTINGS:
            	$component = $this->create_component('UserSettings');
                break;
            case self :: ACTION_USER_APPROVAL_BROWSER:
            	$component = $this->create_component('UserApprovalBrowser');
                break;
            case self :: ACTION_USER_APPROVER:
            	$component = $this->create_component('UserApprover');
                break;
            case self :: ACTION_APPROVE_USER:
            	$component = $this->create_component('UserApprover');
            	Request :: set_get('choice', '1');
                break;
            case self :: ACTION_DENY_USER:
            	$component = $this->create_component('UserApprover');
            	Request :: set_get('choice', '0');
                break;
            case self :: ACTION_EMAIL:
            	$component = $this->create_component('Emailer');
                break;
            case self :: ACTION_REGISTER_INVITED_USER:
            	$component = $this->create_component('Inviter');
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_USERS);
                $component = $this->create_component('AdminUserBrowser');
        }
        $component->run();
    }

    /**
     * Renders the users block and returns it.
     */
    function render_block($block)
    {
        $user_block = UserBlock :: factory($this, $block);
        return $user_block->run();
    }

    /**
     * Counts the users
     * @param $condition
     */
    function count_users($condition = null)
    {
        return UserDataManager :: get_instance()->count_users($condition);
    }

    /**
     * Retrieve the users
     * @param $condition
     * @param $offset
     * @param $count
     * @param $order_property
     */
    function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return UserDataManager :: get_instance()->retrieve_users($condition, $offset, $count, $order_property);
    }

	/*
     * Retrieves a user.
     * @param int $id The id of the user.
     */
    function retrieve_user($id)
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($id);
    }

    function retrieve_user_by_username($username)
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user_by_username($username);
    }

	/*
	 * @see RepositoryDataManager::content_object_deletion_allowed()
     */
    function user_deletion_allowed($user)
    {
        return UserDataManager :: user_deletion_allowed($user);
    }

    /**
     * Gets the available links to display in the platform admin
     * @retun array of links and actions
     */
    public function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('List'), Translation :: get('ListDescription'), Theme :: get_image_path() . 'browse_list.png', $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)));

        if(PlatformSetting :: get('allow_registration', 'user') == 2)
        {
			$links[] = new DynamicAction(Translation :: get('ApproveList'), Translation :: get('ApproveListDescription'), Theme :: get_image_path() . 'browse_list.png', $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_USER_APPROVAL_BROWSER)));
        }

        $links[] = new DynamicAction(Translation :: get('Create'), Translation :: get('CreateDescription'), Theme :: get_image_path() . 'browse_add.png', $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_CREATE_USER)));
        $links[] = new DynamicAction(Translation :: get('Export'), Translation :: get('ExportDescription'), Theme :: get_image_path() . 'browse_export.png', $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_EXPORT_USERS)));
        $links[] = new DynamicAction(Translation :: get('Import'), Translation :: get('ImportDescription'), Theme :: get_image_path() . 'browse_import.png', $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_IMPORT_USERS)));
        $links[] = new DynamicAction(Translation :: get('BuildUserFields'), Translation :: get('BuildUserFieldsDescription'), Theme :: get_image_path() . 'browse_build.png', $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_BUILD_USER_FIELDS)));

        $info = parent :: get_application_platform_admin_links();
        $info['links'] = $links;
        $info['search'] = $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));

        return $info;
    }

	/**
     * Gets the available links to display in the platform admin
     * @retun array of links and actions
     */
    public function get_application_platform_import_links()
    {
        $links = array();
        $links[] = array('name' => Translation :: get('ImportUsers'), 'description' => Translation :: get('ImportUsersDescription'), 'url' => $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_IMPORT_USERS)));

     	return $links;
    }

    /**
     * gets the user editing url
     * @param return the requested url
     */
    function get_user_editing_url($user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_USER, self :: PARAM_USER_USER_ID => $user->get_id()));
    }

    function get_change_user_url($user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_USER, self :: PARAM_USER_USER_ID => $user->get_id()));
    }

    /**
     * gets the user quota url
     * @param return the requested url
     */
    function get_user_quota_url($user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_USER_QUOTA, self :: PARAM_USER_USER_ID => $user->get_id()));
    }

    /**
     * gets the user delete url
     * @param return the requested url
     */
    function get_user_delete_url($user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_USER, self :: PARAM_USER_USER_ID => $user->get_id()));
    }

    function get_manage_user_rights_url($user)
    {
        return $this->get_url(array(Application :: PARAM_APPLICATION => RightsManager :: APPLICATION_NAME, Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_USER_RIGHTS, UserRightManager :: PARAM_USER => $user->get_id()));
    }

    function get_create_buddylist_category_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_BUDDYLIST_CATEGORY));
    }

    function get_delete_buddylist_category_url($category_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_BUDDYLIST_CATEGORY, self :: PARAM_BUDDYLIST_CATEGORY => $category_id));
    }

    function get_update_buddylist_category_url($category_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_BUDDYLIST_CATEGORY, self :: PARAM_BUDDYLIST_CATEGORY => $category_id));
    }

    function get_create_buddylist_item_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_BUDDYLIST_ITEM));
    }

    function get_delete_buddylist_item_url($item_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_BUDDYLIST_ITEM, self :: PARAM_BUDDYLIST_ITEM => $item_id));
    }

    function get_change_buddylist_item_status_url($item_id, $status)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_BUDDYLIST_ITEM_STATUS, self :: PARAM_BUDDYLIST_ITEM => $item_id, 'status' => $status));
    }

    function get_reporting_url($classname, $params)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING, ReportingManager :: PARAM_TEMPLATE_NAME => $classname, ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS => $params));
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

  	function get_user_detail_url($user_id)
    {
    	return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_USER_DETAIL, self :: PARAM_USER_USER_ID => $user_id));
    }

	function get_approve_user_url($user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_USER_APPROVER,
        						    self :: PARAM_USER_USER_ID => $user->get_id(),
        							UserManagerUserApproverComponent :: PARAM_CHOICE => UserManagerUserApproverComponent :: CHOICE_APPROVE));
    }

	function get_deny_user_url($user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_USER_APPROVER,
        						    self :: PARAM_USER_USER_ID => $user->get_id(),
        						    UserManagerUserApproverComponent :: PARAM_CHOICE => UserManagerUserApproverComponent :: CHOICE_DENY));
    }

    function get_email_user_url($user)
    {
    	return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EMAIL,
        						    self :: PARAM_USER_USER_ID => $user->get_id()));
    }

}
?>
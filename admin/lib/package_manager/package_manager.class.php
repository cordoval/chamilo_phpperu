<?php
/**
 * $Id: package_manager.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/component/registration_browser/registration_browser_table.class.php';

class PackageManager extends SubManager
{
    const PARAM_PACKAGE_ACTION = 'action';
    const PARAM_REGISTRATION = 'registration';
    const PARAM_ACTIVATE_SELECTED = 'activate';
    const PARAM_DEACTIVATE_SELECTED = 'deactivate';
    const PARAM_INSTALL_SELECTED = 'install';
    const PARAM_PACKAGE = 'package';
    const PARAM_INSTALL_TYPE = 'type';
    const PARAM_SECTION = 'section';
    
    const ACTION_BROWSE_PACKAGES = 'browse';
    const ACTION_ACTIVATE_PACKAGE = 'activate';
    const ACTION_DEACTIVATE_PACKAGE = 'deactivate';
    const ACTION_REMOTE_PACKAGE = 'remote';
    const ACTION_LOCAL_PACKAGE = 'local';
    const ACTION_ARCHIVE_PACKAGE = 'archive';
    const ACTION_SYNCHRONISE_REMOTE_PACKAGES = 'synchronise';
    const ACTION_INSTALL_PACKAGE = 'install';
    const ACTION_REMOVE_PACKAGE = 'remove';
    
    const INSTALL_REMOTE = 'remote';
    const INSTALL_ARCHIVE = 'archive';
    const INSTALL_LOCAL = 'local';

    function PackageManager($admin_manager)
    {
        parent :: __construct($admin_manager);
        
        $package_action = Request :: get(self :: PARAM_PACKAGE_ACTION);
        if ($package_action)
        {
            $this->set_action($package_action);
        }
        
        $this->parse_input_from_table();
    }

    function run()
    {
        $package_action = $this->get_action();
        
        switch ($package_action)
        {
            case self :: ACTION_BROWSE_PACKAGES :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_ACTIVATE_PACKAGE :
                $component = $this->create_component('Activator');
                break;
            case self :: ACTION_DEACTIVATE_PACKAGE :
                $component = $this->create_component('Deactivator');
                break;
            case self :: ACTION_REMOTE_PACKAGE :
                $component = $this->create_component('Remote');
                break;
            case self :: ACTION_SYNCHRONISE_REMOTE_PACKAGES :
                $component = $this->create_component('Synchroniser');
                break;
            case self :: ACTION_INSTALL_PACKAGE :
                $component = $this->create_component('Installer');
                break;
            case self :: ACTION_LOCAL_PACKAGE :
                $component = $this->create_component('Local');
                break;
            case self :: ACTION_REMOVE_PACKAGE :
                $component = $this->create_component('Remover');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
    }
    
    function set_action($action)
    {
    	$this->set_parameter(self :: PARAM_PACKAGE_ACTION, $action);
    }
    
    function get_action()
    {
    	return $this->get_parameter(self :: PARAM_PACKAGE_ACTION);
    }
    
    function parse_input_from_table()
    {
    	if (isset($_POST['action']))
        {
            $selected_ids = Request :: post(RegistrationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX);
            
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            switch ($_POST['action'])
            {
                case self :: PARAM_ACTIVATE_SELECTED :
                    $this->set_action(self :: ACTION_ACTIVATE_PACKAGE);
                    Request :: set_get(self :: PARAM_REGISTRATION, $selected_ids);
                    break;
                case self :: ACTION_DEACTIVATE_PACKAGE:
                	$this->set_action(self :: ACTION_DEACTIVATE_PACKAGE);
                	Request :: set_get(self :: PARAM_REGISTRATION, $selected_ids);
                    break;
            }
        
        }
    }

    function get_application_component_path()
    {
        return Path :: get_admin_path() . 'lib/package_manager/component/';
    }

    function retrieve_registration($id)
    {
        return $this->get_parent()->retrieve_registration($id);
    }

    function retrieve_registrations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_registrations($condition, $order_by, $offset, $max_objects);
    }

    function count_registrations($condition = null)
    {
        return $this->get_parent()->count_registrations($condition);
    }

    function get_registration_activation_url($registration)
    {
        return $this->get_url(array(self :: PARAM_PACKAGE_ACTION => self :: ACTION_ACTIVATE_PACKAGE, self :: PARAM_REGISTRATION => $registration->get_id()));
    }

    function get_registration_deactivation_url($registration)
    {
        return $this->get_url(array(self :: PARAM_PACKAGE_ACTION => self :: ACTION_DEACTIVATE_PACKAGE, self :: PARAM_REGISTRATION => $registration->get_id()));
    }

    function get_registration_removal_url($registration)
    {
        return $this->get_url(array(self :: PARAM_PACKAGE_ACTION => self :: ACTION_REMOVE_PACKAGE, self :: PARAM_SECTION => $registration->get_type(), self :: PARAM_PACKAGE => $registration->get_id()));
    }

    function get_remote_package_installation_url($remote_package)
    {
        return $this->get_url(array(self :: PARAM_PACKAGE_ACTION => self :: ACTION_INSTALL_PACKAGE, self :: PARAM_INSTALL_TYPE => self :: INSTALL_REMOTE, self :: PARAM_PACKAGE => $remote_package->get_id()));
    }

    /**
     * @see AdminManager :: count_remote_packages()
     */
    function count_remote_packages($condition = null)
    {
        return $this->get_parent()->count_remote_packages($condition);
    }

    /**
     * @see AdminManager :: retrieve_remote_package()
     */
    function retrieve_remote_package($id)
    {
        return $this->get_parent()->retrieve_remote_package($id);
    }

    /**
     * @see AdminManager :: retrieve_remote_packages()
     */
    function retrieve_remote_packages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_remote_packages($condition, $order_by, $offset, $max_objects);
    }
}
?>
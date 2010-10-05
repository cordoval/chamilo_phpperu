<?php
require_once dirname(__FILE__) . '/../cas_account_data_manager/cas_account_data_manager.class.php';
require_once dirname(__FILE__) . '/../cas_account.class.php';

class CasAccountManager extends SubManager
{
    const PARAM_ACCOUNT_ID = 'account_id';
    const PARAM_CAS_ACCOUNT_ACTION = 'action';

    const ACTION_CREATE = 'creator';
    const ACTION_BROWSE = 'browser';
    const ACTION_UPDATE = 'updater';
    const ACTION_DELETE = 'deleter';
    const ACTION_ACTIVATE = 'activater';
    const ACTION_DEACTIVATE = 'deactivater';

    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    function CasAccountManager($rights_manager)
    {
        parent :: __construct($rights_manager);
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function retrieve_cas_accounts($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return CasAccountDataManager :: get_instance()->retrieve_cas_accounts($condition, $offset, $count, $order_property);
    }

    function count_cas_accounts($conditions = null)
    {
        return CasAccountDataManager :: get_instance()->count_cas_accounts($conditions);
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_CAS_ACCOUNT_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }

    function get_update_cas_account_url($cas_account)
    {
        return $this->get_url(array(self :: PARAM_CAS_ACCOUNT_ACTION => self :: ACTION_UPDATE, self :: PARAM_ACCOUNT_ID => $cas_account->get_id()));
    }

    function get_delete_cas_account_url($cas_account)
    {
        return $this->get_url(array(self :: PARAM_CAS_ACCOUNT_ACTION => self :: ACTION_DELETE, self :: PARAM_ACCOUNT_ID => $cas_account->get_id()));
    }

    function get_activate_cas_account_url($cas_account)
    {
        return $this->get_url(array(self :: PARAM_CAS_ACCOUNT_ACTION => self :: ACTION_ACTIVATE, self :: PARAM_ACCOUNT_ID => $cas_account->get_id()));
    }

    function get_deactivate_cas_account_url($cas_account)
    {
        return $this->get_url(array(self :: PARAM_CAS_ACCOUNT_ACTION => self :: ACTION_DEACTIVATE, self :: PARAM_ACCOUNT_ID => $cas_account->get_id()));
    }
}
?>
<?php
require_once dirname(__FILE__) . '/../cas_user_data_manager.class.php';
require_once dirname(__FILE__) . '/../cas_user_request.class.php';

class CasUserManager extends WebApplication
{
    const APPLICATION_NAME = 'cas_user';

    const PARAM_REQUEST_ID = 'request_id';

    const ACTION_BROWSE = 'browser';
    const ACTION_VIEW = 'viewer';
    const ACTION_DELETE = 'deleter';
    const ACTION_EDIT = 'editor';
    const ACTION_CREATE = 'creator';
    const ACTION_ACCEPT = 'accepter';
    const ACTION_REJECT = 'rejecter';
    const ACTION_ACCOUNT = 'account';

    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     * Constructor
     * @param int $user_id
     */
    public function PhotoGalleryManager($user)
    {
        parent :: __construct($user);
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
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    function count_cas_user_requests($condition)
    {
        return CasUserDataManager :: get_instance()->count_cas_user_requests($condition);
    }

    function retrieve_cas_user_requests($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return CasUserDataManager :: get_instance()->retrieve_cas_user_requests($condition, $offset, $max_objects, $order_by);
    }

    function get_update_cas_user_request_url($cas_user_request)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT, self :: PARAM_REQUEST_ID => $cas_user_request->get_id()));
    }

    function get_delete_cas_user_request_url($cas_user_request)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_REQUEST_ID => $cas_user_request->get_id()));
    }

    function get_accept_cas_user_request_url($cas_user_request)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ACCEPT, self :: PARAM_REQUEST_ID => $cas_user_request->get_id()));
    }

    function get_reject_cas_user_request_url($cas_user_request)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REJECT, self :: PARAM_REQUEST_ID => $cas_user_request->get_id()));
    }

}
?>
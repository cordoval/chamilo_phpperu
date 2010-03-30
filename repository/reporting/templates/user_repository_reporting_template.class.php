<?php
/**
 * $Id: user_repository_reporting_template.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.reporting.templates
 * @todo:
 * Template configuration:
 * Able to change name, description etc
 * 2 listboxes: one with available reporting blocks for the app, one with
 * reporting blocks already in template.
 */
require_once dirname(__FILE__) . '/../blocks/user_repository_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/user_document_type_repository_reporting_block.class.php';

class UserRepositoryReportingTemplate extends ReportingTemplate
{

    function UserRepositoryReportingTemplate($parent)
    {
        parent :: __construct($parent);

        $this->add_reporting_block($this->get_user_repository_block());
        $this->add_reporting_block($this->get_user_document_type_repository_block());
    }

    function get_application()
    {
        return UserManager :: APPLICATION_NAME;
    }

    function display_context()
    {
        $html = array();

//        $user_id = Request :: get(UserManager :: PARAM_USER_USER_ID);
//        if ($user_id)
//        {
//            $user = UserDataManager :: get_instance()->retrieve_user($user_id);
//            $html[] = Translation :: get('User') . ': ' . $user->get_fullname();
//        }

        return implode("\n", $html);
    }

    function get_user_repository_block()
    {
        $user_repository_block = new UserRepositoryReportingBlock($this);

        $user_id = Request :: get(UserManager :: PARAM_USER_USER_ID);
        if ($user_id)
        {
            $user_repository_block->set_user_id($user_id);
            $this->set_parameter(UserManager :: PARAM_USER_USER_ID, Session :: get_user_id());
        }

        return $user_repository_block;
    }

    function get_user_document_type_repository_block()
    {
        $user_repository_block = new UserDocumentTypeRepositoryReportingBlock($this);

        $user_id = Request :: get(UserManager :: PARAM_USER_USER_ID);
        if ($user_id)
        {
            $user_repository_block->set_user_id($user_id);
            $this->set_parameter(UserManager :: PARAM_USER_USER_ID, Session :: get_user_id());
        }

        return $user_repository_block;
    }
}
?>
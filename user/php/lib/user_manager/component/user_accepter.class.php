<?php
/**
 * @package user.lib.user_manager.component
 * @author Hans De Bisschop
 */

require_once dirname(__FILE__) . '/user_approver.class.php';

class UserManagerUserAccepterComponent extends UserManagerUserApproverComponent implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Request :: set_get(self :: PARAM_CHOICE, 1);
        parent :: run();
    }
}
?>
<?php
/**
 * @package user.lib.user_manager.component
 * @author Hans De Bisschop
 */

require_once dirname(__FILE__) . '/active_changer.class.php';

class UserManagerDeactivatorComponent extends UserManagerActiveChangerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Request :: set_get(self :: PARAM_ACTIVE, 0);
        parent :: run();
    }
}
?>
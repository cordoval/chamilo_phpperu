<?php
/**
 * $Id: subscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/subscribe.class.php';

class UserToolSubscribeAsAdminComponent extends UserToolSubscribeComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Request :: set_get(self :: PARAM_STATUS, 1);
        parent :: run();
    }
}
?>
<?php
/**
 * $Id: user.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_manager.component
 */
/**
 * Admin component
 */
class RightsManagerUserComponent extends RightsManager implements AdministrationComponent, DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        UserRightManager :: launch($this);
    }
}
?>
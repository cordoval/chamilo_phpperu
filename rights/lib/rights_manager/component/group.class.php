<?php
/**
 * @package rights.lib.rights_manager.component
 */
class RightsManagerGroupComponent extends RightsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        GroupRightManager :: launch($this);
    }
}
?>
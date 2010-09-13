<?php
/**
 * @package rights.lib.rights_manager.component
 */
class RightsManagerLocaterComponent extends RightsManager implements AdministrationComponent, DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        LocationManager :: launch($this);
    }
}
?>
<?php
/**
 * @package rights.lib.rights_manager.component
 */
class RightsManagerLocaterComponent extends RightsManager implements AdministrationComponent
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
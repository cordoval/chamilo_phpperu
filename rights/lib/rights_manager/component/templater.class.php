<?php
/**
 * @package rights.lib.rights_manager.component
 */
class RightsManagerTemplaterComponent extends RightsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        RightsTemplateManager :: launch($this);
    }
}
?>
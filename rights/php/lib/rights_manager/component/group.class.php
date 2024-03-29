<?php
namespace rights;

use common\libraries\AdministrationComponent;
use common\libraries\DelegateComponent;

/**
 * @package rights.lib.rights_manager.component
 */
class RightsManagerGroupComponent extends RightsManager implements AdministrationComponent, DelegateComponent
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
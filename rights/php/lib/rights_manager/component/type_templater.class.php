<?php
namespace rights;

use common\libraries\AdministrationComponent;
use common\libraries\DelegateComponent;

/**
 * @package rights.lib.rights_manager.component
 */
class RightsManagerTypeTemplaterComponent extends RightsManager implements AdministrationComponent, DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        TypeTemplateManager :: launch($this);
    }
}
?>
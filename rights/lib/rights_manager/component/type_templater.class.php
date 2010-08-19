<?php
/**
 * $Id: templater.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_manager.component
 */
/**
 * Admin component
 */
class RightsManagerTypeTemplaterComponent extends RightsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $manager = new TypeTemplateManager($this);
        $manager->run();
    }
}
?>
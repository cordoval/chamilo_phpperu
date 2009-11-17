<?php
/**
 * $Id: install_manager_component.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager
 */
abstract class InstallManagerComponent extends CoreApplicationComponent
{

    /**
     * Constructor
     * @param InstallManager $install_manager The install manager which
     * provides this component
     */
    protected function InstallManagerComponent($install_manager)
    {
        parent :: __construct($install_manager);
    }
}
?>
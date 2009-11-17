<?php
/**
 * $Id: migration_manager_component.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.migration_manager
 * 
 * A MigrationManagerComponent is an abstract class that represents a component that is used
 * in the migrationmanager
 *
 * @author Sven Vanpoucke
 */
abstract class MigrationManagerComponent extends CoreApplicationComponent
{

    protected function MigrationManagerComponent($migration_manager)
    {
        parent :: __construct($migration_manager);
    }
}
?>
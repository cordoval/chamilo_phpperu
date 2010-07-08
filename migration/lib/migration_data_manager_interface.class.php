<?php
/**
 * $Id: migration_data_manager.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib
 * @author Van Wayenbergh David
 * @author Vanpoucke Sven
 */

/**
 * Interface for the databamanagers
 */
interface MigrationDataManagerInterface
{   
    // CRUD for failed_elements
    public function delete_failed_element(FailedElement $failed_element);

    public function update_failed_element(FailedElement $failed_element);

    public function create_failed_element(FailedElement $failed_element);

    public function count_failed_elements($conditions = null);

    public function retrieve_failed_elements($condition = null, $offset = null, $count = null, $order_property = null);
    
    // CRUD for file_recovery
    
    public function delete_file_recovery(FileRecovery $file_recovery);

    public function update_file_recovery(FileRecovery $file_recovery);

    public function create_file_recovery(FileRecovery $file_recovery);

    public function count_file_recoveries($conditions = null);

    public function retrieve_file_recoveries($condition = null, $offset = null, $count = null, $order_property = null);
    
    // CRUD for id_reference
    
    public function delete_id_reference(IdReference $id_reference);

    public function update_id_reference(IdReference $id_reference);

    public function create_id_reference(IdReference $id_reference);

    public function count_id_references($conditions = null);

    public function retrieve_id_references($condition = null, $offset = null, $count = null, $order_property = null);
    
    // CRUD for migration_block
    
    public function delete_migration_block(MigrationBlock $migration_block);

    public function update_migration_block(MigrationBlock $migration_block);

    public function create_migration_block(MigrationBlock $migration_block);

    public function count_migration_blocks($conditions = null);

    public function retrieve_migration_blocks($condition = null, $offset = null, $count = null, $order_property = null);
    
}

?>
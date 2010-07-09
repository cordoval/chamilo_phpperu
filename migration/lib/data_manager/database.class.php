<?php
/**
 * $Id: database_group_data_manager.class.php 232 2009-11-16 10:11:48Z vanpouckesven $
 * @package group.lib.datamanager
 */
require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
 *  @author Sven Vanpoucke
 *  @author Hans De Bisschop
==============================================================================
 */

class DatabaseMigrationDataManager extends Database implements MigrationDataManagerInterface
{
    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('migration_');
    }

	public function delete_failed_element(FailedElement $failed_element) 
	{
		$condition = new EqualityCondition(FailedElement :: PROPERTY_ID, $failed_element->get_id());
        return $this->delete(FailedElement :: get_table_name(), $condition);
	}

	public function update_failed_element(FailedElement $failed_element) 
	{
		$condition = new EqualityCondition(FailedElement :: PROPERTY_ID, $failed_element->get_id());
        return $this->update($failed_element, $condition);
	}

	public function create_failed_element(FailedElement $failed_element) 
	{
		return $this->create($failed_element);
	}

	public function count_failed_elements($conditions = null) 
	{
		return $this->count_objects(FailedElement :: get_table_name(), $conditions);
	}

	public function retrieve_failed_elements($condition = null, $offset = null, $count = null, $order_property = null) 
	{
		return $this->retrieve_objects(FailedElement :: get_table_name(), $condition, $offset, $count, $order_property);
	}

	public function delete_file_recovery(FileRecovery $file_recovery) 
	{
		$condition = new EqualityCondition(FileRecovery :: PROPERTY_ID, $file_recovery->get_id());
        return $this->delete(FileRecovery :: get_table_name(), $condition);
	}

	public function update_file_recovery(FileRecovery $file_recovery) 
	{
		$condition = new EqualityCondition(FileRecovery :: PROPERTY_ID, $file_recovery->get_id());
        return $this->update($file_recovery, $condition);
	}

	public function create_file_recovery(FileRecovery $file_recovery) 
	{
		return $this->create($file_recovery);
	}

	public function count_file_recoveries($conditions = null) 
	{
		return $this->count_objects(FileRecovery :: get_table_name(), $conditions);
	}

	public function retrieve_file_recoveries($condition = null, $offset = null, $count = null, $order_property = null) 
	{
		return $this->retrieve_objects(FileRecovery :: get_table_name(), $condition, $offset, $count, $order_property);
	}

	public function delete_id_reference(IdReference $id_reference) 
	{
		$condition = new EqualityCondition(IdReference :: PROPERTY_ID, $id_reference->get_id());
        return $this->delete(IdReference :: get_table_name(), $condition);
	}

	public function update_id_reference(IdReference $id_reference)
	{
		$condition = new EqualityCondition(IdReference :: PROPERTY_ID, $id_reference->get_id());
        return $this->update($id_reference, $condition);
	}

	public function create_id_reference(IdReference $id_reference) 
	{
		return $this->create($id_reference);
	}

	public function count_id_references($conditions = null) 
	{
		return $this->count_objects(IdReference :: get_table_name(), $conditions);
	}

	public function retrieve_id_references($condition = null, $offset = null, $count = null, $order_property = null) 
	{
		return $this->retrieve_objects(IdReference :: get_table_name(), $condition, $offset, $count, $order_property);
	}
	
	public function delete_migration_block_registration(MigrationBlockRegistration $migration_block_registration) 
	{
		$condition = new EqualityCondition(MigrationBlockRegistration :: PROPERTY_ID, $migration_block_registration->get_id());
        return $this->delete(MigrationBlockRegistration :: get_table_name(), $condition);
	}

	public function update_migration_block_registration(MigrationBlockRegistration $migration_block_registration)
	{
		$condition = new EqualityCondition(MigrationBlockRegistration :: PROPERTY_ID, $migration_block_registration->get_id());
        return $this->update($migration_block_registration, $condition);
	}

	public function create_migration_block_registration(MigrationBlockRegistration $migration_block_registration) 
	{
		return $this->create($migration_block_registration);
	}

	public function count_migration_block_registrations($conditions = null) 
	{
		return $this->count_objects(MigrationBlockRegistration :: get_table_name(), $conditions);
	}

	public function retrieve_migration_block_registrations($condition = null, $offset = null, $count = null, $order_property = null) 
	{
		return $this->retrieve_objects(MigrationBlockRegistration :: get_table_name(), $condition, $offset, $count, $order_property);
	}
}
?>
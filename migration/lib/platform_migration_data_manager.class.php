<?php

/**
 * Interface to determine some obligated functions for each datamanager
 */

Interface PlatformMigrationDataManager
{
	/**
	 * Retrieves all objects from a table
	 * @param MigrationDataClass $data_class
	 * @param int $offset
	 * @param int $count
	 */
	function retrieve_all_objects($data_class, $offset, $count);
	
	/**
	 * Counts all objects from a table
	 * @param MigrationDataClass $data_class
	 */
    function count_all_objects($data_class);
}

?>
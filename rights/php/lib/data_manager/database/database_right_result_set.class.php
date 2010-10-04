<?php
/**
 * $Id: database_right_result_set.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.data_manager.database
 */
require_once dirname(__FILE__) . '/../../../../common/database/result_set.class.php';
/**
 * This class represents a resultset which represents a set of courses.
 */
class DatabaseRightResultSet extends ResultSet
{
    /**
     * The datamanager used to retrieve objects from the repository
     */
    private $data_manager;
    /**
     * An instance of DB_result
     */
    private $handle;

    /**
     * Create a new resultset for handling a set of learning objects
     * @param RepositoryDataManager $data_manager The datamanager used to
     * retrieve objects from the repository
     * @param DB_result $handle The handle to retrieve records from a database
     * resultset
     */
    function DatabaseRightResultSet($data_manager, $handle)
    {
        $this->data_manager = $data_manager;
        $this->handle = $handle;
    }

    /*
	 * Inherited
	 */
    function next_result()
    {
        if ($record = $this->handle->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            return $this->data_manager->record_to_right($record);
        }
        return null;
    }

    /*
	 * Inherited
	 */
    function size()
    {
        return $this->handle->numRows();
    }

    /*
	 * Inherited
	 */
    function skip($count)
    {
        for($i = 0; $i < $count; $i ++)
        {
            $this->handle->fetchRow();
        }
    }
}
?>
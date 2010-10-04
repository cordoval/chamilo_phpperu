<?php
/**
 * $Id: database_complex_content_object_item_result_set.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.data_manager.database
 */
require_once Path :: get_library_path() . 'database/result_set.class.php';
/**
 * Resultset to hold a set of complex learning object items
 */
class DatabaseComplexContentObjectItemResultSet extends ResultSet
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
     * Flag to know if the $handle contains all properties of the course
     * category
     */
    private $single_type;

    /**
     * Create a new resultset for handling a set of learning objects
     * @param RepositoryDataManager $data_manager The datamanager used to
     * retrieve objects from the repository
     * @param DB_result $handle The handle to retrieve records from a database
     * resultset
     * @param boolean $single_type True if the handle holds all properties of
     * the learning objects (so when retrieving the learning objects, the
     * datamanager shouldn't perform additional queries)
     */
    function DatabaseComplexContentObjectItemResultSet($data_manager, $handle, $single_type)
    {
        $this->data_manager = $data_manager;
        $this->handle = $handle;
        $this->single_type = $single_type;
    }

    /*
	 * Inherited
	 */
    function next_result()
    {
        if ($record = $this->handle->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $ref = $record[ComplexContentObjectItem :: PROPERTY_REF];
            $type = $this->data_manager->determine_content_object_type($ref);
            return $this->data_manager->record_to_complex_content_object_item($record, $type, $this->single_type);
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
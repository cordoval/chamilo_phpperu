<?php
/**
 * $Id: object_result_set.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.database
 */
require_once dirname(__FILE__) . '/record_result_set.class.php';
/**
 * This class represents a resultset which represents a set of objects.
 */
class ObjectResultSet extends RecordResultSet
{
    /**
     * The datamanager used to retrieve objects from the repository
     */
    private $data_manager;
    
    /**
     * The classname to map the object to
     */
    private $class_name;

    /**
     * Create a new resultset for handling a set of learning objects
     * @param DataManager $data_manager The datamanager used to
     * retrieve objects from the repository
     * @param DB_result $handle The handle to retrieve records from a database
     * resultset
     */
    function ObjectResultSet($data_manager, $handle, $class_name)
    {
        parent :: __construct($handle);
        $this->data_manager = $data_manager;
        $this->class_name = $class_name;
    }

    /*
	 * Inherited
	 */
    function next_result()
    {
    	$handle = $this->get_handle();
    	if (MDB2 :: isError($handle))
    	{
    		dump($this->get_handle());
    	}
        if ($record = $this->get_handle()->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $this->increment_current();
            return $this->data_manager->record_to_object($record, $this->class_name);
        }
        return null;
    }
}
?>
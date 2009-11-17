<?php
/**
 * $Id: record_result_set.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.database
 */
require_once dirname(__FILE__) . '/result_set.class.php';
/**
 * This class represents a resultset which represents a set of records.
 */
class RecordResultSet extends ResultSet
{
    const POSITION_FIRST = 'first';
    const POSITION_LAST = 'last';
    const POSITION_SINGLE = 'single';
    const POSITION_MIDDLE = 'middle';
    
    /**
     * An instance of an MDB2_result
     */
    private $handle;
    
    /**
     * The current position
     * @var int
     */
    private $current;

    /**
     * Create a new resultset for handling a set of records
     * @param MDB2DB_result $handle The handle to retrieve records from a database resultset
     */
    function RecordResultSet($handle)
    {
        $this->handle = $handle;
    }

    /*
	 * Inherited
	 */
    function next_result()
    {
        if ($record = $this->get_handle()->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $this->increment_current();
            return $record;
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

    function is_empty()
    {
        return $this->size() == 0;
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

    function current()
    {
        return $this->current;
    }

    function position()
    {
        $current = $this->current();
        $size = $this->size();
        
        if ($current == 1 && $size == 1)
        {
            return self :: POSITION_SINGLE;
        }
        elseif ($size > 1 && $current == $size)
        {
            return self :: POSITION_LAST;
        }
        elseif ($size > 1 && $current == 1)
        {
            return self :: POSITION_FIRST;
        }
        else
        {
            return self :: POSITION_MIDDLE;
        }
    }

    function is_first()
    {
        return ($this->position() == self :: POSITION_FIRST || $this->is_single());
    }

    function is_last()
    {
        return ($this->position() == self :: POSITION_LAST || $this->is_single());
    }

    function is_middle()
    {
        return ($this->position() == self :: POSITION_MIDDLE || $this->is_single());
    }

    function is_single()
    {
        return ($this->position() == self :: POSITION_SINGLE);
    }

    function get_handle()
    {
        return $this->handle;
    }

    function get_data_manager()
    {
        return $this->data_manager;
    }

    function increment_current()
    {
        $this->current ++;
    }
}
?>
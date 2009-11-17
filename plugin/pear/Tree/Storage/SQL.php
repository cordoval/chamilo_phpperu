<?php

/**
 * Require parent class definition.
 */
require_once 'Tree/Storage.php';

class Tree_Storage_SQL extends Tree_Storage {
    /**
     * dsn that was connected to
     *
     * @var object
     * @access private
     */
    var $dsn = null;

    /**
     * Database connection object.
     *
     * @var    object
     * @access private
     */
    var $dbc = null;

    /**
     * Table prefix
     * Prefix for all db tables the container has.
     *
     * @var    string
     * @access public
     */
    var $prefix = 'liveuser_';

    /**
     * Table configuration
     *
     * @var    array
     * @access public
     */
    var $tables = array();

    /**
     * All fields with their types
     *
     * @var    array
     * @access public
     */
    var $fields = array();

    /**
     * All fields with their alias
     *
     * @var    array
     * @access public
     */
    var $alias = array();

    function insert($table, $data)
    {
        
    }

    function update($table, $data, $filters)
    {
        
    }

    function remove($table, $filters)
    {
          
    }

    function select()
    {
    
    }
}
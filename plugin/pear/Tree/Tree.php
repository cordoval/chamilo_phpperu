<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2005 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Wolfram Kriesing <wolfram@kriesing.de>                      |
// +----------------------------------------------------------------------+
//
//  $Id: Tree.php 137 2009-11-09 13:24:37Z vanpouckesven $

/**
 * Include PEAR
 */

require_once 'PEAR.php';

/**
*   the DB interface to the tree class
*
*   @access     public
*   @author     Wolfram Kriesing <wolfram@kriesing.de>
*   @version    2001/06/27
*   @package    Tree
*/

define('TREE_ERROR',       -1);
define('TREE_ERROR_NOT_IMPLEMENTED',    -2);
define('TREE_ERROR_ELEMENT_NOT_FOUND',  -3);
define('TREE_ERROR_INVALID_NODE_NAME',  -4);
define('TREE_ERROR_MOVE_TO_CHILDREN',   -5);
define('TREE_ERROR_PARENT_ID_MISSED',   -6);
define('TREE_ERROR_INVALID_PARENT',     -7);
define('TREE_ERROR_EMPTY_PATH',         -8);
define('TREE_ERROR_INVALID_PATH',       -9);
define('TREE_ERROR_DB_ERROR',           -10);
define('TREE_ERROR_PATH_SEPARATOR_EMPTY', -11);
define('TREE_ERROR_CANNOT_CREATE_FOLDER', -12);
define('TREE_ERROR_UNKOWN_ERROR', -13);


class Tree
{
     /**
     * @var array   you need to overwrite this array and give the keys/
     *              that are allowed
     */
    var $_forceSetOption = false;

    /**
     * put proper value-keys are given in each class, depending
     * on the implementation only some options are needed or allowed,
     * see the classes which extend this one
     *
     * @access public
     * @var    array   saves the options passed to the constructor
     */
    var $conf =  array(
        'whereAddOn' => '',
        'table' => '',
        // since the internal names are fixed, to be portable between different
        // DB tables with different column namings, we map the internal name
        // to the real column name using this array here, if it stays empty
        // the internal names are used, which are:
        // id, left, right
        'fields' => array(
            'id' => array('type' => 'integer', 'name' => 'id'),
            'name' => array('type' => 'text', 'name' => 'name'),
            // since mysql at least doesnt support 'left' ...
            'left'      =>  array('type' => 'text', 'name' => 'l'),
            // ...as a column name we set default to the first
            //letter only
            'right'     =>  array('type' => 'text', 'name' => 'r'),
            // parent id
            'parent_id'  =>  array('type' => 'integer', 'name' => 'parent')
        ),
        'order' => 'id'
    );

    /**
     * Storage Container
     *
     * @var object
     */
    var $_storage = null;

    /**
     * Factory
     *
     * @static
     * @access  public
     * @return  object
     * @param   string
     * @param   string
     * @param   array
     */
/*    function &factory($type = 'Simple', $container = 'MDB2', $options = array())
    {
        $type = ucfirst(strtolower($type));
        $container = strtoupper($container);
        $class = 'Tree_'. $type .'_'. $container;
        require_once strtr($class, '_', '/') .'.php';
        $tree = &new $class($options);
        if (Tree::isError($e = $tree->setup($options))) {
            return $e;
        }
        return $tree;
    }
*/

    /**
     * setup an object which works on trees that are temporarily saved in
     * memory dont use with huge trees, suggested is a maximum size of tree
     * of 1000-5000 elements since the entire tree is read at once
     * from the data source. Use this to instanciate a class of a tree, i.e:
     * - need the entire tree at once
     * - want to work on the tree w/o db-access for every call
     * since this set of classes loads the entire tree into the memory, you
     * should be aware about the size of the tree you work on using this class.
     * For one you should know how efficient this kind of tree class is on
     * your data source (i.e. db) and what effect it has reading the entire
     * tree at once. On small trees, like upto about 1000 elements an instance
     * of this class will give you very powerful means to manage/modify
     * the tree no matter from which data source it comes, either
     * from a nested-DB, simple-DB, XML-File/String or whatever is implemented.
     *
     * @version    2002/02/05
     * @access public
     * @author Wolfram Kriesing <wolfram@kriesing.de>
     * @param  string  the kind of data source this class shall work on
     *                 initially, you can still switch later, by using
     *                 "setDataSource" to i.e. export data from a DB to XML,
     *                 or whatever implementation might exist some day.
     *                 currently available types are: 'DBsimple', 'XML'
     *
     * @param  mixed   the dsn, or filename, etc., empty i.e. for XML
     *                 if you use setupByRawData
     */
    function &factoryMemory($config)
    {
        // if anyone knows a better name it would be great to change it.
        // since "setupMemory" kind of reflects it but i think it's not obvious
        // if you dont know what is meant
        include_once 'Tree/Memory.php';
        $memory = new Tree_Memory($config);
        return $memory;
    }

    /**
     * setup an object that works on trees where each element(s) are read
     * on demand from the given data source actually this was intended to serve
     * for nested trees which are read from the db up on demand, since it does
     * not make sense to read a huge tree into the memory when you only want
     * to access one level of this tree. In short: an instance returned by
     * this method works on a tree by mapping every request (such as getChild,
     * getParent ...) to the data source defined to work on.
     *
     * @version  2002/02/05
     * @access   public
     * @author   Wolfram Kriesing <wolfram@kriesing.de>
     * @param    string  the type of tree you want, currently only DBnested
     *                   is supported
     * @param    string  the connection string, for DB* its a DSN, for XML
     *                   it would be the filename
     * @param    array   the options you want to set
     */
    function &factoryDynamic($config)
    {
        // "dynamic" stands for retreiving a tree(chunk) dynamically when needed,
        // better name would be great :-)
        if (in_array(strtoupper($config['storage']['name']), array('DB', 'MDB', 'MDB2'))) {
            $name = 'SQL';
        } else {
            $name = strtoupper($config['storage']['name']);
        }

        $name .= strtolower($config['type']);
        include_once "Tree/Dynamic/$name.php";
        $className = 'Tree_Dynamic_' . $name;
        $dynamic = new $className($config);
        return $dynamic;
    }

    /**
     * this is just a wrapper around the two setup methods above
     * some example calls:
     * <code>
     * $tree = Tree::setup(
     *              'Dynamic_DBnested',
     *              'mysql://root@localhost/test',
     *              array('table'=>'nestedTree')
     *          );
     * $tree = Tree::setup(
     *              'Memory_DBsimple',
     *              'mysql://root@localhost/test',
     *              array('table'=>'simpleTree')
     *          );
     * $tree = Tree::setup(
     *                  'Memory_XML',
     *                  '/path/to/some/xml/file.xml'
     *          );
     * </code>
     *
     * you can call the following too, but the functions/classes are not
     * implemented yet or not finished.
     * <code>
     * $tree = Tree::setup(
     *              'Memory_DBnested',
     *              'mysql://root@localhost/test',
     *              array('table'=>'nestedTree')
     *          );
     * $tree = Tree::setup('Dynamic_XML', '/path/to/some/xml/file.xml');
     * </code>
     *
     * and those would be really cool to have one day:
     * LDAP, Filesystem, WSDL, ...
     *
     * @access    private
     * @version   2002/03/07
     * @author    Wolfram Kriesing <wolfram@kriesing.de>
     * @param string    the type of tree you want, currently only Memory|
     *                  Dynamic_DBnested|XML|... is supported
     * @param string    the connection string, for DB* its a DSN,
     *                  for XML it would be the filename
     * @param array     the options you want to set
     */
    function &factory($config)
    {
        if (!isset($config['container'])) {
            return Tree::raiseError(TREE_ERROR, null, null, 'Config option container wasn\t set.');
        }
        $method = 'factory' . $config['container'];
        return Tree::$method($config);
    }

    /**
     * Returns an instance of a storage Container
     *
     * @param  array        configuration array to pass to the storage container
     * @param string        $classprefix Prefix of the class that will be used.
     * @return object|false will return an instance of a Storage container
     *                      or false upon error
     *
     * @author LiveUser
     * @access protected
     */
    function &storageFactory(&$confArray, $classprefix = 'Tree_')
    {
        $storageName = $classprefix . 'Storage_' . $confArray['name'];
        if (!Tree::loadClass($storageName) && count($confArray) <= 1) {
            $return = false;
            return $return;
        // if the storage container does not exist try the next one in the stack
        }

        $storage = new $storageName();
        if (PEAR::isError($storage->init($confArray))) {
            $return = false;
            return $return;
        }

        return $storage;
    }

    /**
     * Load the storage container
     *
     * @access  public
     * @param  mixed         Name of array containing the configuration.
     * @return  boolean true on success or false on failure
     */
    function init($conf)
    {
        if (!isset($conf['storage'])) {
            return Tree::raiseError(TREE_ERROR, null, null, 'Missing storage configuration array');
        }

        if (is_array($conf)) {
            $keys = array_keys($conf);
            foreach ($keys as $key) {
                if (isset($this->$key)) {
                    $this->$key =& $conf[$key];
                }
            }
        }

        $this->_storage = Tree::storageFactory($conf['storage']);
        if ($this->_storage === false) {
            return Tree::raiseError(TREE_ERROR, null, null, 'Could not instanciate storage container');
        }

        return true;
    }

    /**
     * Loads a PEAR class.
     *
     * @param  string   classname to load
     * @param  bool     if errors should be supressed from the stack
     * @return bool  true success or false on failure
     *
     * @access public
     */
    function loadClass($classname, $supress_error = false)
    {
        if (!Tree::classExists($classname)) {
            $filename = str_replace('_', '/', $classname).'.php';
            @include_once($filename);
            if (!Tree::classExists($classname) && !$supress_error) {
                if (!Tree::fileExists($filename)) {
                    $msg = 'File for the class does not exist ' . $classname;
                } else {
                    $msg = 'Parse error in the file for class' . $classname;
                }
//                 PEAR_ErrorStack::staticPush('LiveUser', LIVEUSER_ERROR_CONFIG,
//                     'exception', array(), $msg);
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if a file exists in the include path.
     *
     * @param  string  filename
     * @return bool true success and false on error
     *
     * @access public
     */
    function fileExists($filename)
    {
        // safe_mode does notwork with is_readable()
        if (ini_get('safe_mode')) {
            $fp = @fopen($file, 'r', true);
            if (is_resource($fp)) {
                @fclose($fp);
                return true;
            }
        } else {
            $dirs = explode(PATH_SEPARATOR, ini_get('include_path'));
            foreach ($dirs as $dir) {
                if (is_readable($dir . DIRECTORY_SEPARATOR . $file)) {
                    return true;
                }
            }
        }
    }

    /**
     * Checks if a class exists without triggering __autoload
     *
     * @param  string  classname
     * @return bool true success and false on error
     *
     * @access public
     */
    function classExists($classname)
    {
        if (version_compare(phpversion(), '5.0', '>=')) {
            return class_exists($classname, false);
        }
        return class_exists($classname);
    }

    /**
     * Clobbers two arrays together.
     *
     * Function taken from the user notes of array_merge_recursive function
     * and may be called statically
     *
     * @param  array        array that should be clobbered
     * @param  array        array that should be clobbered
     * @return array|false  array on success and false on error
     *
     * @access public
     * @author kc@hireability.com
     */
    function arrayMergeClobber($a1, $a2)
    {
        if (!is_array($a1) || !is_array($a2)) {
            return false;
        }
        foreach ($a2 as $key => $val) {
            if (is_array($val) && array_key_exists($key, $a1) && is_array($a1[$key])) {
                $a1[$key] = Tree::arrayMergeClobber($a1[$key], $val);
            } else {
                $a1[$key] = $val;
            }
        }
        return $a1;
    }

    /**
     * This method is used to communicate an error and invoke error
     * callbacks etc.  Basically a wrapper for PEAR::raiseError
     * without the message string.
     *
     * @param mixed    integer error code, or a PEAR error object (all
     *                 other parameters are ignored if this parameter is
     *                 an object
     *
     * @param int      error mode, see PEAR_Error docs
     *
     * @param mixed    If error mode is PEAR_ERROR_TRIGGER, this is the
     *                 error level (E_USER_NOTICE etc).  If error mode is
     *                 PEAR_ERROR_CALLBACK, this is the callback function,
     *                 either as a function name, or as an array of an
     *                 object and method name.  For other error modes this
     *                 parameter is ignored
     * @param string   Extra debug information.  Defaults to the last
     *                 query and native error code.
     *
     * @return object  a PEAR error object
     *
     * @see PEAR_Error
     */
    function &raiseError($code = null, $mode = null, $options = null, $userinfo = null)
    {
        return PEAR::raiseError(null, $code, $mode, $options, $userinfo, 'Tree_Error', true);
    }

    /**
     * Return a textual error message for a MDB2 error code
     *
     * @param   int|array $value integer error code,
                                null to get the current error code-message map,
                                or an array with a new error code-message map
     * @return  string  error message, or false if the error code was
     *                  not recognized
     * @access public
     */
    function errorMessage($value = null)
    {
         // make the variable static so that it only has to do the defining on the first call
        static $errorMessages;

         // define the varies error messages
        if (is_array($value)) {
            $errorMessages = $value;
            return MDB2_OK;
        } elseif (!isset($errorMessages)) {
            $errorMessages = array(
                TREE_ERROR       => 'Unkown error',
                TREE_ERROR_NOT_IMPLEMENTED    => 'This method is currently not implemented',
                TREE_ERROR_INVALID_PATH       => 'Invalid Path',
                TREE_ERROR_DB_ERROR           => 'Database error',
                TREE_ERROR_PARENT_ID_MISSED   => 'Parent ID is missing',
                TREE_ERROR_MOVE_TO_CHILDREN   => 'Move to children',
                TREE_ERROR_ELEMENT_NOT_FOUND  => 'Element not found',
                TREE_ERROR_PATH_SEPARATOR_EMPTY => 'Path separator empty',
                TREE_ERROR_INVALID_NODE_NAME  => 'Invalid node name',
                TREE_ERROR_UNKOWN_ERROR => 'Unkown error',
            );
        }

        if (is_null($value)) {
            return $errorMessages;
        }

        // If this is an error object, then grab the corresponding error code
        if (PEAR::isError($value)) {
            $value = $value->getCode();
        }

        // return the textual error message corresponding to the code
        return isset($errorMessages[$value]) ? $errorMessages[$value] :
                                    $errorMessages[TREE_ERROR];
    }

    // {{{ isError()

    /**
     * Tell whether a value is a Tree error.
     *
     * @param   mixed $data   the value to test
     * @param   int   $code   if $data is an error object, return true
     *                        only if $code is a string and
     *                        $obj->getMessage() == $code or
     *                        $code is an integer and $obj->getCode() == $code
     * @access  public
     * @return  bool    true if parameter is an error
     */
    function isError($data, $code = null)
    {
        if (is_a($data, 'Tree_Error')) {
            if (is_null($code)) {
                return true;
            } elseif (is_string($code)) {
                return $data->getMessage() == $code;
            } else {
                $code = (array)$code;
                return in_array($data->getCode(), $code);
            }
        }

        return false;
    }

    // }}}

    // {{{ getChildrenIds()

    /**
     * get the ids of the children of the given element
     *
     * @version 2002/02/06
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   integer ID of the element that the children shall be
     *                  retreived for
     * @param   integer how many levels deep into the tree
     * @return  mixed   an array of all the ids of the children of the element
     *                  with id=$id, or false if there are no children
     */
    function getChildrenIds($id, $levels = 1)
    {
        // returns false if no children exist
        if (!($children = $this->getChildren($id, $levels))) {
            return array();
        }
        // return an empty array, if you want to know
        // if there are children, use hasChildren
        if ($children && count($children)) {
            foreach ($children as $aChild) {
                $childrenIds[] = $aChild['id'];
            }
        }

        return $childrenIds;
    }

    // }}}
    // {{{ getAllChildren()

    /**
     * gets all the children and grand children etc.
     *
     * @version 2002/09/30
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   integer ID of the element that the children shall be
     *                  retreived for
     *
     * @return  mixed   an array of all the children of the element with
     *                  id=$id, or false if there are no children
     */
     // FIXXXME remove this method and replace it by getChildren($id,0)
    function getAllChildren($id)
    {
        $retChildren = false;
        if ($children = $this->hasChildren($id)) {
            $retChildren = $this->_getAllChildren($id);
        }

        return $retChildren;
    }

    // }}}
    // {{{ _getAllChildren()

    /**
     * this method gets all the children recursively
     *
     * @see getAllChildren()
     * @version 2002/09/30
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   integer ID of the element that the children shall be
     *                  retreived for
     *
     * @return  mixed   an array of all the ids of the children of the element
     *                  with id=$id, or false if there are no children
     */
    function &_getAllChildren($id)
    {
        $retChildren = array();
        if ($children = $this->getChildren($id)) {
            foreach ($children as $key => $child) {
                $retChildren[] = &$children[$key];
                $retChildren = array_merge($retChildren,
                        $this->_getAllChildren($child['id']));
            }
        }

        return $retChildren;
    }

    // }}}
    // {{{ getAllChildrenIds()

    /**
     * gets all the children-ids and grand children-ids
     *
     * @version 2002/09/30
     * @access  public
     * @author  Kriesing <wolfram@kriesing.de>
     * @param   integer ID of the element that the children shall
     *          be retreived for
     *
     * @return  mixed   an array of all the ids of the children of the element
     *                  with id=$id,
     *                  or false if there are no children
     */
    function getAllChildrenIds($id)
    {
        $childrenIds = array();
        if ($allChildren = $this->getAllChildren($id)) {
            $childrenIds = array();
            foreach ($allChildren as $node) {
                $childrenIds[] = $node['id'];
            }
        }

        return $childrenIds;
    }

    // }}}
    // {{{ getParents()

    /**
     * this gets all the preceeding nodes, the parent and it's parent and so on
     *
     * @version 2002/08/19
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   integer the id of the element for which the parent_id shall
     *                  be retreived
     * @return  array   of the parent nodes including the node with id $id
     */
    function getParents($id)
    {
        $path = $this->getPath($id);
        $parents = array();
        if (count($path)) {
            foreach ($path as $node) {
                $parents[] = $node;
            }
        }

        return $parents;
    }

    // }}}
    // {{{ getParentsIds()

    /**
     * get the ids of the parents and all it's parents and so on
     * it simply returns the ids of the elements returned by getParents()
     *
     * @see getParents()
     * @version 2002/08/19
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   integer $id the id of the element for which the parent_id
     *          shall be retreived
     *
     * @return     array   of the ids
     */
    function getParentsIds($id)
    {
        $parents = $this->getParents($id);
        $parentsIds = array();
        if (count($parents)) {
            foreach ($parents as $node) {
                $parentsIds[] = $node['id'];
            }
        }

        return $parentsIds;
    }

    // }}}
    // {{{ getPathAsString()

    /**
     * returns the path as a string
     *
     * @access  public
     * @version 2002/03/28
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed   $id     the id of the node to get the path for
     * @param   integer If offset is positive, the sequence will
     *                  start at that offset in the array .  If
     *                  offset is negative, the sequence will start that far
     *                  from the end of the array.
     * @param   integer If length is given and is positive, then
     *                  the sequence will have that many elements in it. If
     *                  length is given and is negative then the
     *                  sequence will stop that many elements from the end of
     *                  the array. If it is omitted, then the sequence will
     *                  have everything from offset up until the end
     *                  of the array.
     * @param   string  you can tell the key the path shall be used to be
     *                  constructed with i.e. giving 'name' (=default) would
     *                  use the value of the $element['name'] for the node-name
     *                  (thanks to Michael Johnson).
     *
     * @return  array   this array contains all elements from the root
     *                  to the element given by the id
     */
    function getPathAsString($id, $seperator = '/',
                                $offset = 0, $length = 0, $key = 'name')
    {
        $path = $this->getPath($id);
        foreach ($path as $aNode) {
            $pathArray[] = $aNode[$key];
        }

        if ($offset) {
            if ($length) {
                $pathArray = array_slice($pathArray, $offset, $length);
            } else {
                $pathArray = array_slice($pathArray, $offset);
            }
        }

        $pathString = '';
        if (count($pathArray)) {
            $pathString = implode($seperator, $pathArray);
        }

        return $pathString;
    }

    // }}}


    //
    //  abstract methods, those should be overwritten by the implementing class
    //

    // {{{ getPath()

    /**
     * gets the path to the element given by its id
     *
     * @abstract
     * @version 2001/10/10
     * @access  public
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed   $id     the id of the node to get the path for
     * @return  array   this array contains all elements from the root
     *                  to the element given by the id
     */
    function getPath($id)
    {
        return Tree::raiseError('TREE_ERROR_NOT_IMPLEMENTED');
    }

    // }}}
    // {{{ getLevel()

    /**
     * get the level, which is how far below the root the element
     * with the given id is
     *
     * @abstract
     * @version    2001/11/25
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      mixed   $id     the id of the node to get the level for
     *
     */
    function getLevel($id)
    {
        return Tree::raiseError('TREE_ERROR_NOT_IMPLEMENTED');
    }

    // }}}
    // {{{ isChildOf()

    /**
     * returns if $childId is a child of $id
     *
     * @abstract
     * @version    2002/04/29
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      int     id of the element
     * @param      int     id of the element to check if it is a child
     * @param      boolean if this is true the entire tree below is checked
     * @return     boolean true if it is a child
     */
    function isChildOf($id, $childId, $checkAll = true)
    {
        return Tree::raiseError('TREE_ERROR_NOT_IMPLEMENTED');
    }

    // }}}
    // {{{ getIdByPath()

    /**
     *
     *
     */
    function getIdByPath($path, $startId = 0, $nodeName = 'name', $seperator = '/')
    {
        return Tree::raiseError('TREE_ERROR_NOT_IMPLEMENTED');
    }

    // }}}
    // {{{ getDepth()

    /**
     * return the maximum depth of the tree
     *
     * @version    2003/02/25
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @return     int     the depth of the tree
     */
    function getDepth()
    {
        return $this->_treeDepth;
    }

    // }}}

    //
    //  PRIVATE METHODS
    //

    // {{{ _preparePath()

    /**
     * gets the path to the element given by its id
     *
     * @version 2003/05/11
     * @access  private
     * @author  Wolfram Kriesing <wolfram@kriesing.de>
     * @param   mixed   $id     the id of the node to get the path for
     * @return  array   this array contains the path elements and the sublevels
     *                  to substract if no $cwd has been given.
     */
    function _preparePath($path, $cwd = '/', $separator = '/')
    {
        $elems = explode($separator, $path);
        $cntElems = count($elems);

        // beginning with a slash
        if (empty($elems[0])) {
            $beginSlash = true;
            array_shift($elems);
            --$cntElems;
        }

        // ending with a slash
        if (empty($elems[$cntElems - 1])) {
            $endSlash = true;
            array_pop($elems);
            --$cntElems;
        }

        // Get the real path, and the levels
        // to substract if required
        $down = 0;
        while ($elems[0] == '..') {
            array_shift($elems);
            ++$down;
        }

        if ($down >= 0 && $cwd == '/') {
            $down = 0;
            $_elems = array();
            $sublevel = 0;
            $_elems = array();
        } else {
            list($_elems, $sublevel) = $this->_preparePath($cwd);
        }

        $i = 0;
        foreach ($elems as $val) {
            if (trim($val) == '') {
                return Tree::raiseError('TREE_ERROR_INVALID_PATH');
            }

            if ($val == '..') {
                $i == 0 ? ++$down : --$i;
            } else {
                $_elems[++$i] = $val;
            }
        }

        if (count($_elems) < 1) {
            return Tree::raiseError('TREE_ERROR_EMPTY_PATH');
        }

        return array($_elems, $sublevel);
    }

    // }}}
    // {{{ _prepareResults()

    /**
     * prepare multiple results
     *
     * @see        _prepareResult()
     * @access     private
     * @version    2002/03/03
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      array   the data to prepare
     * @return     array   prepared results
     */
    function _prepareResults($results)
    {
        $nResults = array();
        foreach ($results as $key => $value) {
            $nResults[$key] = $this->_prepareResult($value);
        }

        return $nResults;
    }
    // }}}
    // {{{ _prepareResult()

    /**
     * map back the index names to get what is expected
     *
     * @access     private
     * @version    2002/03/03
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      array   a result
     * @return     array   the prepared result
     */
    function _prepareResult($result)
    {
        if (isset($this->conf['fields'])) {
            foreach ($this->conf['fields'] as $key => $columnName) {
                if (isset($result[$columnName['name']]) && $key != $columnName['name']) {
                    $result[$key] = $result[$columnName['name']];
                    unset($result[$columnName['name']]);
                }
            }
        }

        return $result;
    }

    // }}}
    // {{{ _getColName()

    /**
     * this method retrieves the real column name, as used in the DB
     * since the internal names are fixed, to be portable between different
     * DB-column namings, we map the internal name to the real column name here
     *
     * @access     private
     * @version    2002/03/02
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      string  the internal name used
     * @return     string  the real name of the column
     */
    function _getColName($name)
    {
        if (
            isset($this->conf['fields']) &&
            isset($this->conf['fields'][$name]['name'])
        ) {
            return $this->conf['fields'][$name]['name'];
        }

        return $name;
    }

    // }}}

    /*******************************************************************************/
    /************************ METHODS FROM Tree_Options ****************************/
    /*******************************************************************************/

    // {{{ setOption()

    /**
     *
     * @access public
     * @author Stig S. Baaken
     * @param  string  the option name
     * @param  mixed   the value for this option
     * @param  boolean if set to true options are also set
     *                 even if no key(s) was/were found in the options property
     */
    function setOption($option, $value, $force = false)
    {
        if ($option == 'fields') {
            return false;
        }
        // if the value is an array extract the keys
        // and apply only each value that is set
        if (is_array($value)) {
            // so we dont override existing options inside an array
            // if an option is an array
            foreach ($value as $key => $aValue) {
                Tree::setOption(array($option, $key), $aValue);
            }

            return true;
        }

        if (is_array($option)) {
            $mainOption = $option[0];
            $options = "['".implode("']['",$option)."']";
            $evalCode = "\$this->conf".$options." = \$value;";
        } else {
            $evalCode = "\$this->conf[\$option] = \$value;";
            $mainOption = $option;
        }

        if (
            $force == true
            || isset($this->conf[$mainOption])
        ) {
            eval($evalCode);
            return true;
        }
        return false;
    }

    // }}}
    // {{{ setOptions()

    /**
     * set a number of options which are simply given in an array
     *
     * @access public
     * @param  array   the values to set
     * @param  boolean if set to true options are also set even if no key(s)
     *                 was/were found in the options property
     */
    function setOptions($options, $force = false)
    {
        if (is_array($options) && count($options)) {
            foreach ($options as $key => $value) {
                $this->setOption($key, $value, $force);
            }
        }
    }

    // }}}
    // {{{ getOption()

    /**
     *
     * @access     public
     * @author     copied from PEAR: DB/commmon.php
     * @param      boolean true on success
     */
    function getOption($option)
    {
        if (isset($this->conf[$option])) {
            return $this->conf[$option];
        }
        return false;
    }

    // }}}
    // {{{ getOptions()

    /**
     * returns all the options
     *
     * @version    02/05/20
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @return     string      all options as an array
     */
    function getOptions()
    {
        return $this->conf;
    }

    // }}}
}

/**
 * Tree_Error implements a class for reporting portable database error
 * messages.
 *
 * @package Tree
 */
class Tree_Error extends PEAR_Error
{
    /**
     * Tree_Error constructor.
     *
     * @param mixed   $code      Tree error code, or string with error message.
     * @param integer $mode      what 'error mode' to operate in
     * @param integer $level     what error level to use for
     *                           $mode & PEAR_ERROR_TRIGGER
     * @param smixed  $debuginfo additional debug info, such as the last query
     */
    function Tree_Error($code = TREE_ERROR, $mode = PEAR_ERROR_RETURN,
              $level = E_USER_NOTICE, $debuginfo = NULL)
    {
        if (is_int($code)) {
            $this->PEAR_Error('Tree Error: ' . Tree::errorMessage($code), $code,
                $mode, $level, $debuginfo);
        } else {
            $this->PEAR_Error("Tree Error: $code", TREE_ERROR, $mode, $level,
                $debuginfo);
        }
    }
}
?>
<?php

/**
 * $Id: dokeos185_data_manager.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once (dirname(__FILE__) . '/../../lib/old_migration_data_manager.class.php');
require_once 'MDB2.php';

/**
 * Class that connects to the old dokeos185 system
 *
 * @author Sven Vanpoucke
 * @author David Van Wayenbergh
 */
class Dokeos185DataManager extends OldMigrationDataManager
{
    /**
     * MDB2 instance
     * @var MDB2Connection
     */
    private $db;
    private $_configuration;
    private static $move_file;

    function Dokeos185DataManager($old_directory)
    {
        $this->get_configuration($old_directory);
        $this->initialize();
    }

    /**
     * Gets the configuration file of the old Dokeos 1.8.5
     * @param String $old_directory
     */
    function get_configuration($old_directory)
    {
        $old_directory = 'file://' . $old_directory;

        if (file_exists($old_directory) && is_dir($old_directory))
        {
            $config_file = $old_directory . '/main/inc/conf/configuration.php';
            if (file_exists($config_file) && is_file($config_file))
            {
                require_once ($config_file);
                $this->_configuration = $_configuration;
            }
        }
    }

    /**
     * Function to validate the dokeos 185 settings given in the wizard
     * @return true if settings are valid, otherwise false
     */
    function validate_settings()
    {
        if (mysql_connect($this->_configuration['db_host'], $this->_configuration['db_user'], $this->_configuration['db_password']))
        {

            if (mysql_select_db($this->_configuration['main_database']) && mysql_select_db($this->_configuration['statistics_database']) && mysql_select_db($this->_configuration['user_personal_database']))
                return true;
        }

        return false;
    }

    /**
     * Connect to the dokeos185 database with login data from the $$this->_configuration
     * @param String $dbname with databasename
     */
    function initialize()
    {
        PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array(get_class(), 'handle_error'));
        $dbname = 'dokeos_main';
        $param = isset($this->_configuration[$dbname]) ? $this->_configuration[$dbname] : $dbname;
        $host = $this->_configuration['db_host'];
        $pos = strpos($host, ':');

        if ($pos == ! false)
        {
            $array = split(':', $host);
            $socket = $array[count($array) - 1];
            $host = 'unix(' . $socket . ')';
        }

        $dsn = 'mysql://' . $this->_configuration['db_user'] . ':' . $this->_configuration['db_password'] . '@' . $host . '/' . $param;
        $this->db = MDB2 :: connect($dsn, array('debug' => 3, 'debug_handler' => array('Dokeos185DataManager', 'debug')));

        if (PEAR :: isError($this->db))
        {
            die($this->db->getMessage());
        }
        $this->db->setCharset('utf8');
    }

    function set_database($dbname)
    {
        $param = isset($this->_configuration[$dbname]) ? $this->_configuration[$dbname] : $dbname;
        $this->db->setDatabase($param);
    }

    /**
     * This function can be used to handle some debug info from MDB2
     */
    function debug()
    {
        $args = func_get_args();
        // Do something with the arguments
        if ($args[1] == 'query')
        {
            //echo '<pre>';
        //echo($args[2]);
        //echo '</pre>';
        }
    }

    /**
     * Handles pear errors
     */
    static function handle_error($error)
    {
        die(__FILE__ . ':' . __LINE__ . ': ' . $error->getMessage() . // For debugging only. May create a security hazard.
        ' (' . $error->getDebugInfo() . ')');
    }

    /**
     * Get all the users from the dokeos185 database
     * @return array of Dokeos185User
     */
    function get_all_users($offset = null, $limit = null)
    {
        $this->set_database('main_database');
        $query = 'SELECT * FROM ' . $this->get_table_name('user');
        if ($limit != null)
            $this->db->setLimit($limit, $offset);
        $result = $this->db->query($query);
        $users = array();
        while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $users[] = $this->record_to_user($record);

        }
        $result->free();

        foreach ($users as $user)
        {
            $query_admin = 'SELECT * FROM ' . $this->get_table_name('admin') . ' WHERE user_id=' . $user->get_user_id();
            $result_admin = $this->db->query($query_admin);

            if ($result_admin->numRows() == 1)
            {
                $user->set_platformadmin(1);
            }

            $result_admin->free();
        }

        return $users;
    }

    /**
     * Map a resultset record to a Dokeos185User Object
     * @param ResultSetRecord $record from database
     * @return Dokeos185User object with mapped data
     */
    function record_to_user($record)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
        }
        $defaultProp = array();
        foreach (Dokeos185User :: get_default_user_property_names() as $prop)
        {
            $defaultProp[$prop] = $record[$prop];
        }
        return new Dokeos185User($defaultProp);
    }

    /**
     * Generic method to create a classobject from a record
     */
    function record_to_classobject($record, $classname)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
        }
        $defaultProp = array();

        $class = new $classname($defaultProp);

        foreach ($class->get_default_property_names() as $prop)
        {
            $defaultProp[$prop] = $record[$prop];
        }

        $class->set_default_properties($defaultProp);

        return $class;
    }

    /**
     * Move a file to a new place, makes use of Filesystem class
     * Built in checks for same filename
     * @param String $old_rel_path Relative path on the old system
     * @param String $new_rel_path Relative path on the LCMS system
     * @return String $new_filename
     */
    function move_file($old_rel_path, $new_rel_path, $filename)
    {
        $old_path = $this->append_full_path(false, $old_rel_path);
        $new_path = $this->append_full_path(true, $new_rel_path);

        $old_file = $old_path . $filename;
        $new_file = $new_path . $filename;

        if (! file_exists($old_file) || ! is_file($old_file))
            return null;

        $new_filename = Filesystem :: copy_file_with_double_files_protection($old_path, $filename, $new_path, $filename, self :: $move_file);
        $mgdm = MigrationDataManager :: get_instance();
        $mgdm->add_recovery_element($old_file, $new_file);

        return ($new_filename);

    // Filesystem :: remove($old_file);
    }

    /**
     * Create a directory
     * @param boolean $is_new_system Which system the directory has to be created on (true = LCMS)
     * @param String $rel_path Relative path on the chosen system
     */
    function create_directory($is_new_system, $rel_path)
    {
        Filesystem :: create_dir($this->append_full_path($is_new_system, $rel_path));
    }

    /**
     * Function to return the full path
     * @param boolean $is_new_system Which system the directory has to be created on (true = LCMS)
     * @param String $rel_path Relative path on the chosen system
     * @return String $path
     */
    function append_full_path($is_new_system, $rel_path)
    {
        if ($is_new_system)
            $path = Path :: get(SYS_PATH) . $rel_path;
        else
            $path = $this->_configuration['root_sys'] . $rel_path;

        return $path;
    }

    /**
     * Get all the current settings from the dokeos185 database
     * @return array of Dokeos185SettingCurrent
     */
    function get_all_current_settings($offset = null, $limit = null)
    {
        $this->set_database('main_database');
        $query = 'SELECT * FROM ' . $this->get_table_name('settings_current') . ' WHERE category = \'Platform\'';

        if ($limit != null)
            $this->db->setLimit($limit, $offset);

        $result = $this->db->query($query);
        $settings_current = array();
        while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $settings_current[] = $this->record_to_classobject($record, 'Dokeos185SettingCurrent');

        }
        $result->free();

        return $settings_current;
    }

    /**
     * Get the first admin id
     * @return admin_id
     */
    function get_old_admin_id()
    {
        $this->set_database('main_database');
        $query = 'SELECT * FROM ' . $this->get_table_name('user') . ' WHERE EXISTS
	(SELECT user_id FROM ' . $this->get_table_name('admin') . ' WHERE user.user_id = admin.user_id)';
        $result = $this->db->query($query);

        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $id = $record['user_id'];
        $result->free();

        return $id;
    }

    /**
     * Function that gets the item property of a record
     * @param String $db
     * @param String $tool
     * @param int $id
     * @return item_property item property of a record
     */
    function get_item_property($db, $tool, $id)
    {
        $this->set_database($db);

        $query = 'SELECT * FROM ' . $this->get_table_name('item_property') . ' WHERE tool = \'' . $tool . '\' AND ref = ' . $id;

        $result = $this->db->query($query);
        $itemprops = $this->mapper($result, 'Dokeos185ItemProperty');
        $result->free();
        return $itemprops[0];
    }

    /** Get all the documents from a course
     * @param String $course
     * @param int $include_deleted_files
     * @return array of Dokeos185Documents
     */
    function get_all_documents($course, $include_deleted_files, $offset = null, $limit = null)
    {
        $this->set_database($course->get_db_name());
        $query = 'SELECT * FROM ' . $this->get_table_name('document') . ' WHERE filetype <> \'folder\'';

        if ($include_deleted_files != 1)
            $query = $query . ' AND id IN (SELECT ref FROM ' . $this->get_table_name('item_property') . ' WHERE tool=\'document\'' . ' AND visibility <> 2);';
        if ($limit != null)
            $this->db->setLimit($limit, $offset);
        $result = $this->db->query($query);
        $documents = $this->mapper($result, 'Dokeos185Document');

        return $documents;
    }

    /**
     * Generic method for getting all the records of a table
     * @param String $database
     * @param String $tablename
     * @param String $classname
     * @param String $tool_name
     * @return dokeos185 datatype Array of dokeos 185 datatype
     */
    function get_all($database, $tablename, $classname, $tool_name = null, $offset = null, $limit = null)
    {
        $this->set_database($database);
        /*$querycheck = 'SHOW table status like \'' . $tablename . '\'';
        $result = $this->db->query($querycheck);
        if (MDB2 :: isError($result) || $result->numRows() == 0)
        {
            $result->free();
            return false;
        }
        $result->free();*/
        $query = 'SELECT * FROM ' . $this->get_table_name($tablename);
        if ($limit != null)
            $this->db->setLimit($limit, $offset);

        if ($tool_name)
            $query = $query . ' WHERE id IN (SELECT ref FROM ' . $this->get_table_name('item_property') . ' WHERE ' . 'tool=\'' . $tool_name . '\' AND visibility <> 2);';

        $result = $this->db->query($query);
        if (MDB2 :: isError($result))
            return false;

        $list = $this->mapper($result, $classname);
        $result->free();
        return $list;
    }

    /**
     * sets a boolean for move or copy files
     * @param bool $move_file
     */
    static function set_move_file($move_file)
    {
        self :: $move_file = $move_file;
    }

    /**
     * Returns the first available course category
     * @return int code of the first course category
     */
    function get_first_course_category()
    {
        $this->db_lcms_connect();
        $query = 'SELECT code FROM ' . $this->get_table_name('weblcms_course_category');
        $result = $this->db->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $result->free();
        if ($record)
            return $record['code'];

        return null;
    }

    /**
     * Maps the result of the generic get_all method
     * @param resultSet $result
     * @param String $class
     * @return Array with dokeos185 datatypes
     */
    function mapper($result, $class)
    {
        $list = array();
        while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $list[] = $this->record_to_classobject($record, $class);
        }
        $result->free();

        return $list;
    }

    /**
     * Gets all the answer of a question
     * @param String $database
     * @param int $id
     * @return Array of Dokeos185QuizAnswers
     */
    function get_all_question_answer($database, $id)
    {
        $this->set_database($database);

        $query = 'SELECT * FROM ' . $this->get_table_name('quiz_answer') . ' WHERE question_id = ' . $id;
        $result = $this->db->query($query);

        return $this->mapper($result, 'Dokeos185QuizAnswer');

    }

    function count_records($database, $table, $condition = null)
    {
    	$this->set_database($database);
        /*$querycheck = 'SHOW table status like \'' . $table . '\''; dump($querycheck);
        $result = $this->db->query($querycheck);
        if (MDB2 :: isError($result) || $result->numRows() == 0)
        {
            $result->free();
            return 0;
        }
        $result->free();*/

        $query = 'SELECT COUNT(*) as number FROM ' . $this->get_table_name($table);

        $params = array();

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $params);
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
            unset($translator);
        }

        $statement = $this->db->prepare($query);
        $result = $statement->execute($params);
        $statement->free();
        unset($query);
        unset($statement);
        $params = array();
        unset($params);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $result->free();
        return $record['number'];
    }

    static function is_date_column($name)
    {
        return false;
    }

    function escape_column_name($name, $prefix_content_object_properties = false)
    {
        // Check whether the name contains a seperator, avoids notices.
        $contains_table_name = strpos($name, '.');
        if ($contains_table_name === false)
        {
            $table = $name;
            $column = null;
        }
        else
        {
            list($table, $column) = explode('.', $name, 2);
        }

        $prefix = '';

        if (isset($column))
        {
            $prefix = $table . '.';
            $name = $column;
        }
        return $prefix . $this->db->quoteIdentifier($name);
    }

    /**
     * Expands a table identifier to the real table name. Currently, this
     * method prefixes the given table name.
     * @param string $name The table identifier.
     * @return string The actual table name.
     */
    function get_table_name($name)
    {
        $dsn = $this->db->getDSN('array');
        return $dsn['database'] . '.' . $name;
    }
}

?>

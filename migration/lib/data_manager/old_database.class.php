<?php
/**
 * $Id: database.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.data_manager
 */
require_once dirname(__FILE__) . '/../migration_data_manager.class.php';
require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *  @author Sven Vanpoucke
==============================================================================
 */
class DatabaseMigrationDataManager extends MigrationDataManager
{

    /**
     * Table names
     */
    const TEMP_FAILED_ELEMENTS_TABLE = 'temp_failed_elements';
    const TEMP_RECOVERY_TABLE = 'temp_recovery';
    const TEMP_ID_REFERENCE_TABLE = 'temp_id_reference';
    const TEMP_FILES_M5_TABLE = 'temp_files_md5';

    /**
     * The database connection.
     */
    private $connection;
    private $database;

    // Inherited.
    function initialize()
    {
        PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array(get_class(), 'handle_error'));

        $this->connection = Connection :: get_instance()->get_connection();
        $this->connection->setOption('debug_handler', array(get_class($this), 'debug'));

        if (PEAR :: isError($this))
        {
            die($this->connection->getMessage());
        }
        $this->connection->setCharset('utf8');

        $this->database = new Database(array('course' => 'cs'));
        $this->database->set_prefix('weblcms_');

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
     * Create a storage unit in the database
     * @param string $name name of the table
     * @param array $properties properties of the table
     * @param array $indexes indexes of the table
     */
    function create_storage_unit($name, $properties, $indexes)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        $tables = $manager->listTables();
        if (in_array($name, $tables))
        {
            $manager->dropTable($name);
        }

        $name = $this->get_table_name($name);

        $options['charset'] = 'utf8';
        $options['collate'] = 'utf8_unicode_ci';
        if (! MDB2 :: isError($manager->createTable($name, $properties, $options)))
        {
            foreach ($indexes as $index_name => $index_info)
            {
                if ($index_info['type'] == 'primary')
                {
                    $index_info['primary'] = 1;
                    $manager->createConstraint($name, $index_name, $index_info);

                }
                else
                {
                    $manager->createIndex($name, $index_name, $index_info);
                }
            }
        }

    }

    /**
     * Expands a table identifier to the real table name. Currently, this
     * method prefixes the given table name.
     * @param string $name The table identifier.
     * @return string The actual table name.
     */
    function get_table_name($name)
    {
        $dsn = $this->connection->getDSN('array');
        return $dsn['database'] . '.' . $name;
    }

    /**
     * gets the parent_id from a learning object
     *
     * @param int $owner id of the owner of the learning object
     * @param String $type type of the learning object
     * @param String $title title of the learning object
     * @return $record returns a parent_id
     */
    function get_parent_id($owner, $type, $title, $parent = null)
    {
        $title = $this->connection->quote($title, "text", true);

        $query = 'SELECT id FROM ' . $this->get_table_name('repository_content_object') . ' WHERE owner_id=\'' . $owner . '\' AND type=\'' . $type . '\' AND title=' . $title . '';

        if ($parent)
            $query = $query . ' AND parent_id=' . $parent;

        $result = $this->connection->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $result->free();

        return $record['id'];
    }

    /**
     * creates temporary tables in the LCMS-database for the migration
     */
    function create_temporary_tables()
    {
        $dir = dirname(__FILE__);
        $files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES);

        foreach ($files as $file)
        {
            if ((substr($file, - 3) == 'xml'))
            {
                $storage_unit_info = Installer :: parse_xml_file($file);
                $this->create_storage_unit($storage_unit_info['name'], $storage_unit_info['properties'], $storage_unit_info['indexes']);
            }
        }

    }

    /**
     * add a failed migration element to table failed_elements
     * @param String $failed_id ID from the object that failed to migrate
     * @param String $table The table where the failed_id is stored
     */
    function add_failed_element($failed_id, $table)
    {
        $query = 'INSERT INTO ' . $this->get_table_name(self :: TEMP_FAILED_ELEMENTS_TABLE) . ' (failed_id, table_name) VALUES (\'' . $failed_id . '\',\'' . $table . '\')';
        $this->connection->query($query);

    }

    /**
     * add a migrated file to the table recovery to make a rollback action possible
     * @param String $old_path the old path of an element
     * @param String $new_path the new path of an element
     */
    function add_recovery_element($old_path, $new_path)
    {
        $old_path = $this->connection->quote($old_path, "text", true); //str_replace('\'', '\\\'', $old_path);
        $new_path = $this->connection->quote($new_path, "text", true); //str_replace('\'', '\\\'', $new_path);


        $query = 'INSERT INTO ' . $this->get_table_name(self :: TEMP_RECOVERY_TABLE) . '(old_path, new_path) VALUES (' . $old_path . ',' . $new_path . ')';
        $this->connection->query($query);
    }

    /**
     * add an id reference to the table id_reference
     * @param String $old_id The old ID of an element
     * @param String $new_id The new ID of an element
     * @param String $table_name The name of the table where an element is placed
     */
    function add_id_reference($old_id, $new_id, $table_name)
    {
        $query = 'INSERT INTO ' . $this->get_table_name(self :: TEMP_ID_REFERENCE_TABLE) . ' (old_id, new_id, table_name) VALUES (\'' . $old_id . '\',\'' . $new_id . '\',\'' . $table_name . '\')';
        $this->connection->query($query);
    }

    /**
     * Adds an md5 of a file to the database for later checks
     */
    function add_file_md5($user_id, $document_id, $md5)
    {
        $query = 'INSERT INTO ' . $this->get_table_name(self :: TEMP_FILES_M5_TABLE) . ' (user_id, document_id, file_md5) VALUES (\'' . $user_id . '\',\'' . $document_id . '\',\'' . $md5 . '\')';
        $this->connection->query($query);
    }

    /**
     * select an failed migration element from table failed_elements by id
     * @param int $id ID of  an failed migration element
     * @return database-record failed migration record
     */
    function get_failed_element($table_name, $old_id)
    {
        $query = 'SELECT * FROM ' . $this->get_table_name(self :: TEMP_FAILED_ELEMENTS_TABLE) . ' WHERE table_name=\'' . $table_name . '\' AND failed_id=\'' . $old_id . '\'';
        $result = $this->connection->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $result->free();

        if ($record)
            return $record;

        return NULL;
    }

    /**
     * select a recovery element from table recovery by id
     * @param int $id ID of  an recovery element
     * @return database-record recovery record
     */
    function get_recovery_element($id)
    {
        $query = 'SELECT * FROM ' . $this->get_table_name(self :: TEMP_RECOVERY_TABLE) . ' WHERE id = \'' . $id . '\'';
        $result = $this->connection->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $result->free();

        if ($record)
            return $record;

        return NULL;
    }

    /**
     * select an id reference element from table id_reference by id
     * @param int $id ID of  an id_reference element
     * @return database-record id_reference record
     */
    function get_id_reference($old_id, $table_name)
    {
        $query = 'SELECT new_id FROM ' . $this->get_table_name(self :: TEMP_ID_REFERENCE_TABLE) . ' WHERE old_id = \'' . $old_id . '\' AND table_name=\'' . $table_name . '\'';
        $result = $this->connection->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        unset($old_id);
        unset($table_name);
        $result->free();

        if ($record)
            return $record['new_id'];

        return NULL;
    }

    /**
     * Selects a document id from the files_md5 table
     */
    function get_document_from_md5($user_id, $md5)
    {
        $query = 'SELECT document_id FROM ' . $this->get_table_name(self :: TEMP_FILES_M5_TABLE) . ' WHERE user_id = \'' . $user_id . '\' AND file_md5=\'' . $md5 . '\'';
        $result = $this->connection->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $result->free();

        if ($record)
            return $record['document_id'];

        return NULL;
    }

    /**
     * Checks if an authentication method is available in the lcms system
     * @param string $auth_method Authentication method to check for
     * @return true if method is available
     */
    function is_authentication_available($auth_method)
    {
        //TODO: make a authentication method list
        return true;
    }

    /**
     * Checks if a language is available in the lcms system
     * @param string $language Language to check for
     * @return true if language is available
     */
    function is_language_available($language)
    {
        $query = 'SELECT id FROM ' . $this->get_table_name('admin_language') . ' WHERE folder=\'' . $language . '\';';

        $result = $this->connection->query($query);
        return ($result->numRows() > 0);
    }

    /**
     * get the next position
     * @return int next position
     */
    function get_next_position($table_name, $field_name)
    {
        $query = 'SELECT MAX(' . $field_name . ') AS \'highest\' FROM ' . $this->get_table_name($table_name);

        $result = $this->connection->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $number = $record['highest'];

        return ++ $number;
    }

    /**
     * Checks if a code is allready available in a table
     */
    function code_available($table_name, $code)
    {
        $query = 'SELECT * FROM ' . $this->get_table_name($table_name) . ' WHERE ';
        if ($table_name == 'weblcms_course')
            $query = $query . 'code=\'' . $code . '\'';
        else
            $query = $query . 'id=\'' . $code . '\'';
        $result = $this->connection->query($query);
        return ($result->numRows() > 0);
    }

    /**
     * Checks if a visual_code is allready available in a table
     */
    function visual_code_available($visual_code)
    {
        $condition = new EqualityCondition(Course :: PROPERTY_VISUAL, $visual_code);
        return ! ($this->database->count_objects(Course :: get_table_name(), $condition) == 1);
    }

    /**
     * Creates a unix time from the given timestamp
     */
    function make_unix_time($date)
    {
        list($dat, $tim) = explode(" ", $date);
        list($y, $mo, $d) = explode("-", $dat);
        list($h, $mi, $s) = explode(":", $tim);

        return mktime($h, $mi, $s, $mo, $d, $y);
    }

    /**
     * Gets the parent id of weblcmslearningobjectpublicationcategory
     */
    function publication_category_exist($title, $course_code, $tool, $parent = null)
    {
        $title = $this->connection->quote($title, "text", true);
        $query = 'SELECT id FROM ' . $this->get_table_name('weblcms_content_object_publication_category') . ' WHERE name=' . $title . ' AND course_id=\'' . $course_code . '\' AND tool=\'' . $tool . '\'';

        if ($parent)
            $query = $query . ' AND parent_id=' . $parent;
        $result = $this->connection->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $result->free();

        return $record['id'];
    }

    /**
     * Retrieve the document id with give owner and document path
     * @param string $path path of the document
     * @param int $owner
     */
    function get_document_id($path, $owner_id)
    {
        $path = $this->connection->quote($path, "text", true);
        $query = 'SELECT id FROM ' . $this->get_table_name('repository_document') . ' WHERE path=' . $path . ' AND id IN ' . '(SELECT id FROM ' . $this->get_table_name('repository_content_object') . ' WHERE owner_id = ' . $owner_id . ')';

        $result = $this->connection->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $result->free();

        return $record['id'];
    }

    /**
     * Method to retrieve the best owner for an orphan
     * @param string $course course code
     */
    function get_owner($course)
    {
        $query = 'SELECT user_id FROM ' . $this->get_table_name('weblcms_course_user_relation') . ' WHERE course_id = \'' . $course . '\' AND status=1;';

        $result = $this->connection->query($query);
        $owners = array();
        while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $owners[] = $record['user_id'];

        }
        $result->free();

        if (count($owners) == 1)
        {
            return $owners[0];
        }
        else
        {
            $query = 'SELECT CRL.user_id FROM ' . $this->get_table_name('weblcms_course_user_relation') . ' CRL WHERE CRL.user_id IN (
					  SELECT UU.user_id FROM ' . $this->get_table_name('user_user') . ' UU WHERE CONCAT(UU.lastname,\' \',UU.firstname) IN (
			  SELECT C.titular_id FROM ' . $this->get_table_name('weblcms_course') . ' C WHERE C.id = CRL.course_id)) AND CRL.status = 1 AND CRL.course_id = \'' . $course . '\';';

            $result = $this->connection->query($query);
            $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
            $result->free();
            if ($record)
                return $record['user_id'];
            else
            {
                $query = 'SELECT COUNT(LOP.publisher) as count, LOP.publisher FROM ' . $this->get_table_name('weblcms_content_object_publication') . ' LOP WHERE LOP.publisher IN (
						  SELECT CRL.user_id FROM ' . $this->get_table_name('weblcms_course_user_relation') . ' CRL WHERE CRL.course_id = \'' . $course . '\' AND CRL.status = 1) AND
						  LOP.course = \'' . $course . '\' GROUP BY LOP.publisher;';

                $result = $this->connection->query($query);
                $owner_id = - 1;
                $max_published = - 1;

                while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
                {
                    if ($record['count'] > $max_published)
                    {
                        $max_published = $record['count'];
                        $owner_id = $record['publisher'];
                    }

                }
                $result->free();

                if ($owner_id == - 1)
                {
                    $query = 'SELECT user_id FROM ' . $this->get_table_name('user_user') . ' WHERE admin = 1';
                    $result = $this->connection->query($query);
                    $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
                    $owner_id = $record['user_id'];
                }
                return $owner_id;
            }
        }
    }

    /**
     * Retrieves a learning object
     * @param int $lp_id learning object id
     * @param string $tool tool of where the learning object belongs
     */
    function get_owner_content_object($lp_id, $tool)
    {
        $datamanager = RepositoryDataManager :: get_instance();
        $result = $datamanager->retrieve_content_object($lp_id, $tool);
        return $result;
    }

    /**
     * Retrieves a user by full name
     * @param string $fullname the fullname of the user
     */
    function get_user_by_full_name($fullname)
    {
        $fullname = $this->connection->quote($fullname, "text", true);

        $query = 'SELECT user_id FROM ' . $this->get_table_name('user_user') . ' WHERE ' . 'CONCAT(firstname, \' \', lastname) = ' . $fullname . ' OR ' . 'CONCAT(lastname, \' \', firstname) = ' . $fullname;
        $result = $this->connection->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        if ($record)
        {
            return $record['user_id'];
        }
    }
}
?>
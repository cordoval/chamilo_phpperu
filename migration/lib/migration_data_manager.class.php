<?php
/**
 * $Id: migration_data_manager.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib
 * @author Van Wayenbergh David
 * @author Vanpoucke Sven
 */

/**
 * Abstract class from which the system datamanagers can extend
 * also used for communication with LCMS databases
 */
abstract class MigrationDataManager
{
    private static $instance;

    /**
     * Constructor.
     */
    protected function MigrationDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return TrackingDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'MigrationDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /**
     * Create a storage unit in the database
     * @param string $name name of the table
     * @param array $properties properties of the table
     * @param array $indexes indexes of the table
     */
    abstract function create_storage_unit($name, $properties, $indexes);

    /**
     * gets the parent_id from a learning object
     * 
     * @param int $owner id of the owner of the learning object
     * @param String $type type of the learning object
     * @param String $title title of the learning object
     * @return $record returns a parent_id
     */
    abstract function get_parent_id($owner, $type, $title, $parent = null);

    /**
     * creates temporary tables in the LCMS-database for the migration
     */
    abstract function create_temporary_tables();

    /**
     * add a failed migration element to table failed_elements
     * @param String $failed_id ID from the object that failed to migrate
     * @param String $table The table where the failed_id is stored
     */
    abstract function add_failed_element($failed_id, $table);

    /**
     * add a migrated file to the table recovery to make a rollback action possible
     * @param String $old_path the old path of an element
     * @param String $new_path the new path of an element
     */
    abstract function add_recovery_element($old_path, $new_path);

    /**
     * add an id reference to the table id_reference
     * @param String $old_id The old ID of an element
     * @param String $new_id The new ID of an element
     * @param String $table_name The name of the table where an element is placed
     */
    abstract function add_id_reference($old_id, $new_id, $table_name);

    /**
     * Adds an md5 of a file to the database for later checks
     */
    abstract function add_file_md5($user_id, $document_id, $md5);

    /**
     * select an failed migration element from table failed_elements by id
     * @param int $id ID of  an failed migration element
     * @return database-record failed migration record
     */
    abstract function get_failed_element($table_name, $old_id);

    /**
     * select a recovery element from table recovery by id
     * @param int $id ID of  an recovery element
     * @return database-record recovery record
     */
    abstract function get_recovery_element($id);

    /**
     * select an id reference element from table id_reference by id
     * @param int $id ID of  an id_reference element
     * @return database-record id_reference record
     */
    abstract function get_id_reference($old_id, $table_name);

    /**
     * Selects a document id from the files_md5 table
     */
    abstract function get_document_from_md5($user_id, $md5);

    /**
     * Checks if an authentication method is available in the lcms system
     * @param string $auth_method Authentication method to check for
     * @return true if method is available
     */
    abstract function is_authentication_available($auth_method);

    /**
     * Checks if a language is available in the lcms system
     * @param string $language Language to check for
     * @return true if language is available
     */
    abstract function is_language_available($language);

    /**
     * get the next position
     * @return int next position
     */
    abstract function get_next_position($table_name, $field_name);

    /**
     * Checks if a code is allready available in a table
     */
    abstract function code_available($table_name, $code);

    /**
     * Checks if a visual code is allready available in a table
     */
    abstract function visual_code_available($visual_code);

    /**
     * Gets the parent id of weblcmslearningobjectpublicationcategory
     */
    abstract function publication_category_exist($title, $course_code, $tool, $parent = null);

    /**
     * Retrieve the document id with give owner and document path
     * @param string $path path of the document
     * @param int $owner 
     */
    abstract function get_document_id($path, $owner_id);

    /**
     * Method to retrieve the best owner for an orphan
     * @param string $course course code
     */
    abstract function get_owner($course);

    /**
     * Retrieves a learning object 
     * @param int $lp_id learning object id
     * @param string $tool tool of where the learning object belongs
     */
    abstract function get_owner_content_object($lp_id, $tool);

    /**
     * Retrieves a user by full name
     * @param string $fullname the fullname of the user
     */
    abstract function get_user_by_full_name($fullname);

   /**
 	 *	retrieve category
 	 *  if the category does not exist, create a new category
 	 *  return the id
 	 *  
     */
    function get_repository_category_by_name($user_id, $title)
    {
		$dm = RepositoryDataManager :: get_instance();
		$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_NAME, $title);
        $conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $user_id);
        $condition = new AndCondition($conditions);
        
        $categories = $dm->retrieve_categories($condition);
        $category = $categories->next_result();
        if(!$category)
        {
            //Create category for tool in lcms
        	$category = new RepositoryCategory();
        	$category->set_user_id($user_id);
        	$category->set_name($title);
        	$category->set_parent(0);
            
        	//Create category in database
        	$category->create();
        }
 
        return $category->get_id();       
        
    }
    
    
}

?>
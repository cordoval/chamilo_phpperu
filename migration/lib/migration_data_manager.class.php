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
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return MigrationDataManagerInterface The implementation of the migration data manager interface
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
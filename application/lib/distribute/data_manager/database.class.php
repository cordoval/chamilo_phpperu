<?php
/**
 * $Id: database.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute.database
 */
require_once dirname(__FILE__) . '/../announcement_distribution.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Hans De Bisschop
 */

class DatabaseDistributeDataManager extends DistributeDataManager
{
    private $database;

    function initialize()
    {
        $aliases = array();
        $aliases[AnnouncementDistribution :: get_table_name()] = 'dion';
        
        $this->database = new Database($aliases);
        $this->database->set_prefix('distribute_');
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function get_next_announcement_distribution_id()
    {
        return $this->database->get_next_id(AnnouncementDistribution :: get_table_name());
    }

    function create_group_moderator($group_moderator)
    {
        return $this->database->create($group_moderator);
    }

    function create_announcement_distribution($announcement_distribution)
    {
        if ($this->database->create($announcement_distribution))
        {
            $users = $announcement_distribution->get_target_users();
            foreach ($users as $index => $user_id)
            {
                $props = array();
                $props[$this->database->escape_column_name('distribution_id')] = $announcement_distribution->get_id();
                $props[$this->database->escape_column_name('user_id')] = $user_id;
                $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('announcement_distribution_user'), $props, MDB2_AUTOQUERY_INSERT);
            }
            
            $groups = $announcement_distribution->get_target_groups();
            foreach ($groups as $index => $group_id)
            {
                $props = array();
                $props[$this->database->escape_column_name('distribution_id')] = $announcement_distribution->get_id();
                $props[$this->database->escape_column_name('group_id')] = $group_id;
                $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('announcement_distribution_group'), $props, MDB2_AUTOQUERY_INSERT);
            }
            
            return true;
        }
        else
        {
            return false;
        }
    }

    function update_announcement_distribution($distribute_publication)
    {
        $condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_ID, $distribute_publication->get_id());
        return $this->database->update($distribute_publication, $condition);
    }

    function delete_announcement_distribution($distribute_publication)
    {
        $condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_ID, $distribute_publication->get_id());
        return $this->database->delete($distribute_publication->get_table_name(), $condition);
    }

    function count_announcement_distributions($condition = null)
    {
        return $this->database->count_objects(AnnouncementDistribution :: get_table_name(), $condition);
    }

    function retrieve_announcement_distribution($id)
    {
        $condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(AnnouncementDistribution :: get_table_name(), $condition);
    }

    function retrieve_announcement_distributions($condition = null, $offset = null, $max_objects = null, $order_by = array())
    {
        return $this->database->retrieve_objects(AnnouncementDistribution :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_announcement_distribution_target_groups($announcement_distribution)
    {
        return array();
    }

    function retrieve_announcement_distribution_target_users($announcement_distribution)
    {
        return array();
    }
}
?>
<?php
/**
 * $Id: database.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia.data_manager
 */
require_once dirname(__FILE__) . '/../alexia_publication.class.php';
require_once dirname(__FILE__) . '/../alexia_publication_group.class.php';
require_once dirname(__FILE__) . '/../alexia_publication_user.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Hans De Bisschop
 */

class DatabaseAlexiaDataManager extends AlexiaDataManager
{
    private $database;

    function initialize()
    {
        $aliases = array();
        //		$aliases[AlexiaPublication :: get_table_name()] = 'ap';
        //		$aliases[AlexiaPublicationGroup :: get_table_name()] = 'apg';
        //		$aliases[AlexiaPublicationUser :: get_table_name()] = 'apu';


        $this->database = new Database($aliases);
        $this->database->set_prefix('alexia_');
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function get_next_alexia_publication_id()
    {
        return $this->database->get_next_id(AlexiaPublication :: get_table_name());
    }

    function create_alexia_publication($alexia_publication)
    {
        $succes = $this->database->create($alexia_publication);

        foreach ($alexia_publication->get_target_groups() as $group)
        {
            $alexia_publication_group = new AlexiaPublicationGroup();
            $alexia_publication_group->set_alexia_publication($alexia_publication->get_id());
            $alexia_publication_group->set_group_id($group);
            $succes = $alexia_publication_group->create();
        }

        foreach ($alexia_publication->get_target_users() as $user)
        {
            $alexia_publication_user = new AlexiaPublicationUser();
            $alexia_publication_user->set_alexia_publication($alexia_publication->get_id());
            $alexia_publication_user->set_user($user);
            $succes = $alexia_publication_user->create();
        }

        return $succes;
    }

    function update_alexia_publication($alexia_publication, $delete_targets = true)
    {
        $condition = new EqualityCondition(AlexiaPublication :: PROPERTY_ID, $alexia_publication->get_id());
        $succes = $this->database->update($alexia_publication, $condition);

        if ($delete_targets)
        {
            $condition = new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_PUBLICATION, $alexia_publication->get_id());
            $succes = $this->database->delete(AlexiaPublicationGroup :: get_table_name(), $condition);

            $condition = new EqualityCondition(AlexiaPublicationUser :: PROPERTY_PUBLICATION, $alexia_publication->get_id());
            $succes = $this->database->delete(AlexiaPublicationUser :: get_table_name(), $condition);

            foreach ($alexia_publication->get_target_groups() as $group)
            {
                $alexia_publication_group = new AlexiaPublicationGroup();
                $alexia_publication_group->set_publication($alexia_publication->get_id());
                $alexia_publication_group->set_group_id($group);
                $succes = $alexia_publication_group->create();
            }

            foreach ($alexia_publication->get_target_users() as $user)
            {
                $alexia_publication_user = new AlexiaPublicationUser();
                $alexia_publication_user->set_publication($alexia_publication->get_id());
                $alexia_publication_user->set_user($user);
                $succes = $alexia_publication_user->create();
            }
        }

        return $succes;
    }

    function delete_alexia_publication($alexia_publication)
    {
        $condition = new EqualityCondition(AlexiaPublication :: PROPERTY_ID, $alexia_publication->get_id());
        $succes = $this->database->delete($alexia_publication->get_table_name(), $condition);

        $condition = new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_PUBLICATION, $alexia_publication->get_id());
        $succes &= $this->database->delete(AlexiaPublicationGroup :: get_table_name(), $condition);

        $condition = new EqualityCondition(AlexiaPublicationUser :: PROPERTY_PUBLICATION, $alexia_publication->get_id());
        $succes &= $this->database->delete(AlexiaPublicationUser :: get_table_name(), $condition);

        return $succes;
    }

    function count_alexia_publications($condition = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->database->get_alias(AlexiaPublication :: get_table_name());
        $publication_user_alias = $this->database->get_alias(AlexiaPublicationUser :: get_table_name());
        $publication_group_alias = $this->database->get_alias(AlexiaPublicationGroup :: get_table_name());
        $object_alias = $this->database->get_alias(ContentObject :: get_table_name());

        $query = 'SELECT COUNT(*) FROM ' . $this->database->escape_table_name(AlexiaPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->database->escape_column_name(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->get_database()->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(AlexiaPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->database->escape_column_name(AlexiaPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(AlexiaPublicationUser :: PROPERTY_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(AlexiaPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->database->escape_column_name(AlexiaPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(AlexiaPublicationGroup :: PROPERTY_PUBLICATION, $publication_group_alias);

        return $this->database->count_result_set($query, AlexiaPublication :: get_table_name(), $condition);
    }

    function retrieve_alexia_publication($id)
    {
        $condition = new EqualityCondition(AlexiaPublication :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(AlexiaPublication :: get_table_name(), $condition, array(), AlexiaPublication :: CLASS_NAME);
    }

    function retrieve_alexia_publications($condition = null, $offset = null, $max_objects = null, $order_by = array())
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->database->get_alias(AlexiaPublication :: get_table_name());
        $publication_user_alias = $this->database->get_alias(AlexiaPublicationUser :: get_table_name());
        $publication_group_alias = $this->database->get_alias(AlexiaPublicationGroup :: get_table_name());
        $object_alias = $this->database->get_alias(ContentObject :: get_table_name());

        $query = 'SELECT ' . $publication_alias . '.* FROM ' . $this->database->escape_table_name(AlexiaPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->database->escape_column_name(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->get_database()->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(AlexiaPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->database->escape_column_name(AlexiaPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(AlexiaPublicationUser :: PROPERTY_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(AlexiaPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->database->escape_column_name(AlexiaPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(AlexiaPublicationGroup :: PROPERTY_PUBLICATION, $publication_group_alias);

        return $this->database->retrieve_object_set($query, AlexiaPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, AlexiaPublication :: CLASS_NAME);
    }

    function create_alexia_publication_group($alexia_publication_group)
    {
        return $this->database->create($alexia_publication_group);
    }

    function delete_alexia_publication_group($alexia_publication_group)
    {
        $condition = new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_ID, $alexia_publication_group->get_id());
        return $this->database->delete($alexia_publication_group->get_table_name(), $condition);
    }

    function count_alexia_publication_groups($condition = null)
    {
        return $this->database->count_objects(AlexiaPublicationGroup :: get_table_name(), $condition);
    }

    function retrieve_alexia_publication_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(AlexiaPublicationGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, AlexiaPublicationGroup :: CLASS_NAME);
    }

    function create_alexia_publication_user($alexia_publication_user)
    {
        return $this->database->create($alexia_publication_user);
    }

    function delete_alexia_publication_user($alexia_publication_user)
    {
        $condition = new EqualityCondition(AlexiaPublicationUser :: PROPERTY_ID, $alexia_publication_user->get_id());
        return $this->database->delete($alexia_publication_user->get_table_name(), $condition);
    }

    function count_alexia_publication_users($condition = null)
    {
        return $this->database->count_objects(AlexiaPublicationUser :: get_table_name(), $condition);
    }

    function retrieve_alexia_publication_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(AlexiaPublicationUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, AlexiaPublicationUser :: CLASS_NAME);
    }
}
?>
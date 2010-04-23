<?php
/**
 * $Id: database_alexia_data_manager.class.php 238 2009-11-16 14:10:27Z vanpouckesven $
 * @package application.lib.alexia.data_manager
 */
require_once dirname(__FILE__) . '/../alexia_publication.class.php';
require_once dirname(__FILE__) . '/../alexia_publication_group.class.php';
require_once dirname(__FILE__) . '/../alexia_publication_user.class.php';
require_once dirname(__FILE__) . '/../alexia_data_manager_interface.class.php';

/**
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Hans De Bisschop
 */

class DatabaseAlexiaDataManager extends Database implements AlexiaDataManagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('alexia_');
    }

    function create_alexia_publication($alexia_publication)
    {
        $succes = $this->create($alexia_publication);

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

        return $succes;
    }

    function update_alexia_publication($alexia_publication, $delete_targets = true)
    {
        $condition = new EqualityCondition(AlexiaPublication :: PROPERTY_ID, $alexia_publication->get_id());
        $succes = $this->update($alexia_publication, $condition);

        if ($delete_targets)
        {
            $condition = new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_PUBLICATION, $alexia_publication->get_id());
            $succes = $this->delete(AlexiaPublicationGroup :: get_table_name(), $condition);

            $condition = new EqualityCondition(AlexiaPublicationUser :: PROPERTY_PUBLICATION, $alexia_publication->get_id());
            $succes = $this->delete(AlexiaPublicationUser :: get_table_name(), $condition);

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
        $succes = $this->delete($alexia_publication->get_table_name(), $condition);

        $condition = new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_PUBLICATION, $alexia_publication->get_id());
        $succes &= $this->delete(AlexiaPublicationGroup :: get_table_name(), $condition);

        $condition = new EqualityCondition(AlexiaPublicationUser :: PROPERTY_PUBLICATION, $alexia_publication->get_id());
        $succes &= $this->delete(AlexiaPublicationUser :: get_table_name(), $condition);

        return $succes;
    }

    function count_alexia_publications($condition = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->get_alias(AlexiaPublication :: get_table_name());
        $publication_user_alias = $this->get_alias(AlexiaPublicationUser :: get_table_name());
        $publication_group_alias = $this->get_alias(AlexiaPublicationGroup :: get_table_name());
        $object_alias = $this->get_alias(ContentObject :: get_table_name());

        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name(AlexiaPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->escape_column_name(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(AlexiaPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->escape_column_name(AlexiaPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->escape_column_name(AlexiaPublicationUser :: PROPERTY_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(AlexiaPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->escape_column_name(AlexiaPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->escape_column_name(AlexiaPublicationGroup :: PROPERTY_PUBLICATION, $publication_group_alias);

        return $this->count_result_set($query, AlexiaPublication :: get_table_name(), $condition);
    }

    function retrieve_alexia_publication($id)
    {
        $condition = new EqualityCondition(AlexiaPublication :: PROPERTY_ID, $id);
        return $this->retrieve_object(AlexiaPublication :: get_table_name(), $condition, array(), AlexiaPublication :: CLASS_NAME);
    }

    function retrieve_alexia_publications($condition = null, $offset = null, $max_objects = null, $order_by = array())
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->get_alias(AlexiaPublication :: get_table_name());
        $publication_user_alias = $this->get_alias(AlexiaPublicationUser :: get_table_name());
        $publication_group_alias = $this->get_alias(AlexiaPublicationGroup :: get_table_name());
        $object_alias = $this->get_alias(ContentObject :: get_table_name());

        $query = 'SELECT ' . $publication_alias . '.* FROM ' . $this->escape_table_name(AlexiaPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->escape_column_name(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(AlexiaPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->escape_column_name(AlexiaPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->escape_column_name(AlexiaPublicationUser :: PROPERTY_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(AlexiaPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->escape_column_name(AlexiaPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->escape_column_name(AlexiaPublicationGroup :: PROPERTY_PUBLICATION, $publication_group_alias);

        return $this->retrieve_object_set($query, AlexiaPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, AlexiaPublication :: CLASS_NAME);
    }

    function create_alexia_publication_group($alexia_publication_group)
    {
        return $this->create($alexia_publication_group);
    }

    function delete_alexia_publication_group($alexia_publication_group)
    {
        $condition = new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_ID, $alexia_publication_group->get_id());
        return $this->delete($alexia_publication_group->get_table_name(), $condition);
    }

    function count_alexia_publication_groups($condition = null)
    {
        return $this->count_objects(AlexiaPublicationGroup :: get_table_name(), $condition);
    }

    function retrieve_alexia_publication_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(AlexiaPublicationGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, AlexiaPublicationGroup :: CLASS_NAME);
    }

    function create_alexia_publication_user($alexia_publication_user)
    {
        return $this->create($alexia_publication_user);
    }

    function delete_alexia_publication_user($alexia_publication_user)
    {
        $condition = new EqualityCondition(AlexiaPublicationUser :: PROPERTY_ID, $alexia_publication_user->get_id());
        return $this->delete($alexia_publication_user->get_table_name(), $condition);
    }

    function count_alexia_publication_users($condition = null)
    {
        return $this->count_objects(AlexiaPublicationUser :: get_table_name(), $condition);
    }

    function retrieve_alexia_publication_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(AlexiaPublicationUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, AlexiaPublicationUser :: CLASS_NAME);
    }

    // Publication attributes


    function content_object_is_published($object_id)
    {
        return $this->any_content_object_is_published(array($object_id));
    }

    function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, $object_ids);
        return $this->count_objects(AlexiaPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->get_alias(AlexiaPublication :: get_table_name());

                $query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' . $this->escape_table_name(AlexiaPublication :: get_table_name()) . ' AS ' . $pub_alias . ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias . ' ON ' . $this->escape_column_name(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, $pub_alias) . '=' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

                $condition = new EqualityCondition(AlexiaPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
                $translator = new ConditionTranslator($this);
                $query .= $translator->render_query($condition);

                $order = array();
                foreach ($order_properties as $order_property)
                {
                    if ($order_property->get_property() == 'application')
                    {

                    }
                    elseif ($order_property->get_property() == 'location')
                    {

                    }
                    elseif ($order_property->get_property() == 'title')
                    {
                        $order[] = $this->escape_column_name('title') . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    else
                    {
                        $order[] = $this->escape_column_name($order_property->get_property()) . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                }

                if (count($order) > 0)
                    $query .= ' ORDER BY ' . implode(', ', $order);

            }
        }
        else
        {
            $query = 'SELECT * FROM ' . $this->escape_table_name(AlexiaPublication :: get_table_name());
            $condition = new EqualityCondition(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);

        }

        $this->set_limit($offset, $count);
        $res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[AlexiaPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[AlexiaPublication :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[AlexiaPublication :: PROPERTY_PUBLISHED]);
            $info->set_application('alexia');
            //TODO: i8n location string
            $info->set_location(Translation :: get('Alexia'));
            $info->set_url('run.php?application=alexia&go=browse');
            $info->set_publication_object_id($record[AlexiaPublication :: PROPERTY_CONTENT_OBJECT]);

            $publication_attr[] = $info;
        }

        $res->free();

        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name(AlexiaPublication :: get_table_name()) . ' WHERE ' . $this->escape_column_name(AlexiaPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->set_limit(0, 1);
        $res = $this->query($query);

        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[AlexiaPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[AlexiaPublication :: PROPERTY_PUBLISHER]);
        $publication_attr->set_publication_date($record[AlexiaPublication :: PROPERTY_PUBLISHED]);
        $publication_attr->set_application('alexia');
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('Alexia'));
        $publication_attr->set_url('run.php?application=alexia&go=browse');
        $publication_attr->set_publication_object_id($record[AlexiaPublication :: PROPERTY_CONTENT_OBJECT]);

        $res->free();

        return $publication_attr;
    }

    function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if (! $object_id)
        {
            $condition = new EqualityCondition(AlexiaPublication :: PROPERTY_PUBLISHER, $user->get_id());
        }
        else
        {
            $condition = new EqualityCondition(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        }
        return $this->count_objects(AlexiaPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        $publications = $this->retrieve_alexia_publications($condition);

        $succes = true;

        while ($publication = $publications->next_result())
        {
            $succes &= $publication->delete();
        }

        return $succes;
    }

    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->escape_column_name(AlexiaPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name(AlexiaPublication :: PROPERTY_CONTENT_OBJECT)] = $publication_attr->get_publication_object_id();
        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name(AlexiaPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>
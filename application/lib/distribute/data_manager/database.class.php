<?php
/**
 * $Id: database.class.php 238 2009-11-16 14:10:27Z vanpouckesven $
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

	// Publication attributes

	function content_object_is_published($object_id)
    {
        return $this->any_content_object_is_published(array($object_id));
    }

    function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT, $object_ids);
        return $this->database->count_objects(AnnouncementDistribution :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_database()->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->database->get_alias(AnnouncementDistribution :: get_table_name());

            	$query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' .
                		 $this->database->escape_table_name(AnnouncementDistribution :: get_table_name()) . ' AS ' . $pub_alias .
                		 ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias .
                		 ' ON ' . $this->database->escape_column_name(AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT, $pub_alias) . '=' .
                		 $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

                $condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_PUBLISHER, Session :: get_user_id());
                $translator = new ConditionTranslator($this->database);
                $query .= $translator->render_query($condition);

                $order = array();
                foreach($order_properties as $order_property)
                {
                    if ($order_property->get_property() == 'application')
                    {

                    }
                    elseif ($order_property->get_property() == 'location')
                    {

                    }
                    elseif ($order_property->get_property() == 'title')
                    {
                        $order[] = $this->database->escape_column_name('title') . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    else
                    {
                        $order[] = $this->database->escape_column_name($order_property->get_property()) . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                }

                if(count($order) > 0)
                	$query .= ' ORDER BY ' . implode(', ', $order);

            }
        }
        else
        {
            $query = 'SELECT * FROM ' . $this->database->escape_table_name(AnnouncementDistribution :: get_table_name());
           	$condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT, $object_id);
           	$translator = new ConditionTranslator($this->database);
           	$query .= $translator->render_query($condition);

        }

        $this->database->set_limit($offset, $count);
		$res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[AnnouncementDistribution :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[AnnouncementDistribution :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[AnnouncementDistribution :: PROPERTY_PUBLISHED]);
            $info->set_application('alexia');
            //TODO: i8n location string
            $info->set_location(Translation :: get('Alexia'));
            $info->set_url('run.php?application=alexia&go=browse');
            $info->set_publication_object_id($record[AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT]);

            $publication_attr[] = $info;
        }
        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->database->escape_table_name(AnnouncementDistribution :: get_table_name()) . ' WHERE ' . $this->database->escape_column_name(AnnouncementDistribution :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->database->set_limit(0, 1);
        $res = $this->query($query);

        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[AnnouncementDistribution :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[AnnouncementDistribution :: PROPERTY_PUBLISHER]);
        $publication_attr->set_publication_date($record[AnnouncementDistribution :: PROPERTY_PUBLISHED]);
        $publication_attr->set_application('alexia');
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('Alexia'));
        $publication_attr->set_url('run.php?application=alexia&go=browse');
        $publication_attr->set_publication_object_id($record[AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT]);

        return $publication_attr;
    }
    
	function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if(!$object_id)
        {
    		$condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_PUBLISHER, $user->get_id());
        }
        else
        {
        	$condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT, $object_id);
        }
        return $this->database->count_objects(AnnouncementDistribution :: get_table_name(), $condition);
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT, $object_id);
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
        $where = $this->database->escape_column_name(AnnouncementDistribution :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name(AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT)] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(AnnouncementDistribution :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
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
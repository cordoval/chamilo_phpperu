<?php
/**
 * $Id: database.class.php 238 2009-11-16 14:10:27Z vanpouckesven $
 * @package application.lib.wiki.data_manager
 */
require_once dirname(__FILE__) . '/../wiki_publication.class.php';
require_once dirname(__FILE__) . '/../wiki_pub_feedback.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke & Stefan Billiet
 */

class DatabaseWikiDataManager extends WikiDataManager
{
    private $database;

    function initialize()
    {
        $aliases = array();
        $aliases[WikiPublication :: get_table_name()] = 'wion';
        $aliases[WikiPubFeedback :: get_table_name()] = 'wpf';

        $this->database = new Database($aliases);
        $this->database->set_prefix('wiki_');
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function create_wiki_publication($wiki_publication)
    {
        return $this->database->create($wiki_publication);
    }

    function update_wiki_publication($wiki_publication)
    {
        $condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $wiki_publication->get_id());
        return $this->database->update($wiki_publication, $condition);
    }

    function delete_wiki_publication($wiki_publication)
    {
        $condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $wiki_publication->get_id());
        return $this->database->delete($wiki_publication->get_table_name(), $condition);
    }

    function count_wiki_publications($condition = null)
    {
        return $this->database->count_objects(WikiPublication :: get_table_name(), $condition);
    }

    function retrieve_wiki_publication($id)
    {
        $condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $id);
        $object = $this->database->retrieve_object(WikiPublication :: get_table_name(), $condition);
        $object->set_default_property('content_object_id', RepositoryDataManager :: get_instance()->retrieve_content_object($object->get_default_property('content_object_id')));
        return $object;
    }

    function retrieve_wiki_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(WikiPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_wiki_pub_feedback($id)
    {
        $condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $id);
        $object = $this->database->retrieve_object(WikiPubFeedback :: get_table_name(), $condition);

        return $object;
    }

    function retrieve_wiki_pub_feedbacks($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(WikiPubFeedback :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function create_wiki_pub_feedback($feedback)
    {
        return $this->database->create($feedback);
    }

    function update_wiki_pub_feedback($feedback)
    {
        $condition = new EqualityCondition(WikiPubFeedback :: PROPERTY_ID, $feedback->get_id());
        return $this->database->update($feedback, $condition);
    }

    function delete_wiki_pub_feedback($feedback)
    {
        $condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $feedback->get_id());
        return $this->database->delete(WikiPubFeedback :: get_table_name(), $condition);
    }

	// Publication attributes

	function content_object_is_published($object_id)
    {
        return $this->any_content_object_is_published(array($object_id));
    }

    function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(WikiPublication :: PROPERTY_CONTENT_OBJECT, $object_ids);
        return $this->database->count_objects(WikiPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_database()->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->database->get_alias(WikiPublication :: get_table_name());

            	$query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' .
                		 $this->database->escape_table_name(WikiPublication :: get_table_name()) . ' AS ' . $pub_alias .
                		 ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias .
                		 ' ON ' . $this->database->escape_column_name(WikiPublication :: PROPERTY_CONTENT_OBJECT, $pub_alias) . '=' .
                		 $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

                $condition = new EqualityCondition(WikiPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
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
            $query = 'SELECT * FROM ' . $this->database->escape_table_name(WikiPublication :: get_table_name());
           	$condition = new EqualityCondition(WikiPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
           	$translator = new ConditionTranslator($this->database);
           	$query .= $translator->render_query($condition);

        }

        $this->database->set_limit($offset, $count);
		$res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[WikiPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[WikiPublication :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[WikiPublication :: PROPERTY_PUBLISHED]);
            $info->set_application('wiki');
            //TODO: i8n location string
            $info->set_location(Translation :: get('Wiki'));
            $info->set_url('run.php?application=alexia&go=browse');
            $info->set_publication_object_id($record[WikiPublication :: PROPERTY_CONTENT_OBJECT]);

            $publication_attr[] = $info;
        }
        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->database->escape_table_name(WikiPublication :: get_table_name()) . ' WHERE ' . $this->database->escape_column_name(WikiPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->database->set_limit(0, 1);
        $res = $this->query($query);

        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[WikiPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[WikiPublication :: PROPERTY_PUBLISHER]);
        $publication_attr->set_publication_date($record[WikiPublication :: PROPERTY_PUBLISHED]);
        $publication_attr->set_application('wiki');
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('Wiki'));
        $publication_attr->set_url('run.php?application=alexia&go=browse');
        $publication_attr->set_publication_object_id($record[WikiPublication :: PROPERTY_CONTENT_OBJECT]);

        return $publication_attr;
    }

    function count_publication_attributes($type = null, $condition = null)
    {
        $condition = new EqualityCondition(WikiPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
        return $this->database->count_objects(WikiPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(WikiPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        $publications = $this->retrieve_alexia_publications($condition);

        $succes = true;

        while ($publication = $publications->next_result())
        {
            $succes &= $publication->delete();
        }

        return $succes;
    }
    
	function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(WikiPublication :: PROPERTY_ID, $publication_id);
        return $this->database->delete(WikiPublication :: get_table_name(), $condition);
    }

    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name(WikiPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name(WikiPublication :: PROPERTY_CONTENT_OBJECT)] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(WikiPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function query($query)
    {
    	return $this->database->query($query);
    }

}
?>
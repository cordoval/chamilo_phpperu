<?php
/**
 * $Id: database.class.php 210 2009-11-13 13:18:50Z kariboe $
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

    function get_next_wiki_publication_id()
    {
        return $this->database->get_next_id(WikiPublication :: get_table_name());
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

    function get_next_wiki_pub_feedback_id()
    {
        return $this->database->get_next_id(WikiPubFeedback :: get_table_name());
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

}
?>
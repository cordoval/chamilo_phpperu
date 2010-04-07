<?php
require_once dirname(__FILE__) . '/../peer_assessment_publication.class.php';
require_once dirname(__FILE__) . '/../peer_assessment_pub_feedback.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Nick Van Loocke
 */

class DatabasePeerAssessmentDataManager extends PeerAssessmentDataManager
{
    private $database;

    function initialize()
    {
        $aliases = array();
        $aliases[PeerAssessmentPublication :: get_table_name()] = 'wion';
        $aliases[PeerAssessmentPubFeedback :: get_table_name()] = 'wpf';

        $this->database = new Database($aliases);
        $this->database->set_prefix('peer_assessment_');
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function create_peer_assessment_publication($peer_assessment_publication)
    {
        return $this->database->create($peer_assessment_publication);
    }

    function update_peer_assessment_publication($peer_assessment_publication)
    {
        $condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_ID, $peer_assessment_publication->get_id());
        return $this->database->update($peer_assessment_publication, $condition);
    }

    function delete_peer_assessment_publication($peer_assessment_publication)
    {
        $condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_ID, $peer_assessment_publication->get_id());
        return $this->database->delete($peer_assessment_publication->get_table_name(), $condition);
    }

    function count_peer_assessment_publications($condition = null)
    {
        return $this->database->count_objects(PeerAssessmentPublication :: get_table_name(), $condition);
    }

    function retrieve_peer_assessment_publication($id)
    {
        $condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_ID, $id);
        $object = $this->database->retrieve_object(PeerAssessmentPublication :: get_table_name(), $condition);
        $object->set_default_property('content_object_id', RepositoryDataManager :: get_instance()->retrieve_content_object($object->get_default_property('content_object_id')));
        return $object;
    }

    function retrieve_peer_assessment_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(PeerAssessmentPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_peer_assessment_pub_feedback($id)
    {
        $condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_ID, $id);
        $object = $this->database->retrieve_object(PeerAssessmentPubFeedback :: get_table_name(), $condition);

        return $object;
    }

    function retrieve_peer_assessment_pub_feedbacks($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(PeerAssessmentPubFeedback :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function create_peer_assessment_pub_feedback($feedback)
    {
        return $this->database->create($feedback);
    }

    function update_peer_assessment_pub_feedback($feedback)
    {
        $condition = new EqualityCondition(PeerAssessmentPubFeedback :: PROPERTY_ID, $feedback->get_id());
        return $this->database->update($feedback, $condition);
    }

    function delete_peer_assessment_pub_feedback($feedback)
    {
        $condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_ID, $feedback->get_id());
        return $this->database->delete(PeerAssessmentPubFeedback :: get_table_name(), $condition);
    }
    
    // Categories
    
     function create_peer_assessment_publication_category($peer_assessment_publication_category)
    {
        return $this->database->create($peer_assessment_publication_category);
    }

    function update_peer_assessment_publication_category($peer_assessment_publication_category)
    {
        $condition = new EqualityCondition(PeerAssessmentPublicationCategory :: PROPERTY_ID, $peer_assessment_publication_category->get_id());
        return $this->database->update($peer_assessment_publication_category, $condition);
    }

    function delete_peer_assessment_publication_category($peer_assessment_publication_category)
    {
        $condition = new EqualityCondition(PeerAssessmentPublicationCategory :: PROPERTY_ID, $peer_assessment_publication_category->get_id());
        return $this->database->delete($peer_assessment_publication_category->get_table_name(), $condition);
    }

    function count_peer_assessment_publication_categories($conditions = null)
    {
        return $this->database->count_objects(PeerAssessmentPublicationCategory :: get_table_name(), $conditions);
    }

    function retrieve_peer_assessment_publication_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(PeerAssessmentPublicationCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }
    
	function retrieve_max_sort_value($table_name, $column, $condition)
    {
        return $this->database->retrieve_max_sort_value($table_name, $column, $condition);
    }
    

	// Publication attributes

	function content_object_is_published($object_id)
    {
        return $this->any_content_object_is_published(array($object_id));
    }

    function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(PeerAssessmentPublication :: PROPERTY_CONTENT_OBJECT, $object_ids);
        return $this->database->count_objects(PeerAssessmentPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_database()->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->database->get_alias(PeerAssessmentPublication :: get_table_name());

            	$query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' .
                		 $this->database->escape_table_name(PeerAssessmentPublication :: get_table_name()) . ' AS ' . $pub_alias .
                		 ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias .
                		 ' ON ' . $this->database->escape_column_name(PeerAssessmentPublication :: PROPERTY_CONTENT_OBJECT, $pub_alias) . '=' .
                		 $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

                $condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
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
            $query = 'SELECT * FROM ' . $this->database->escape_table_name(PeerAssessmentPublication :: get_table_name());
           	$condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
           	$translator = new ConditionTranslator($this->database);
           	$query .= $translator->render_query($condition);

        }

        $this->database->set_limit($offset, $count);
		$res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[PeerAssessmentPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[PeerAssessmentPublication :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[PeerAssessmentPublication :: PROPERTY_PUBLISHED]);
            $info->set_application('peer_assessment');
            //TODO: i8n location string
            $info->set_location(Translation :: get('PeerAssessment'));
            $info->set_url('run.php?application=peer_assessment&go=browse');
            $info->set_publication_object_id($record[PeerAssessmentPublication :: PROPERTY_CONTENT_OBJECT]);

            $publication_attr[] = $info;
        }
        
        $res->free();
        
        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->database->escape_table_name(PeerAssessmentPublication :: get_table_name()) . ' WHERE ' . $this->database->escape_column_name(PeerAssessmentPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->database->set_limit(0, 1);
        $res = $this->query($query);

        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[PeerAssessmentPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[PeerAssessmentPublication :: PROPERTY_PUBLISHER]);
        $publication_attr->set_publication_date($record[PeerAssessmentPublication :: PROPERTY_PUBLISHED]);
        $publication_attr->set_application('peer_assessment');
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('PeerAssessment'));
        $publication_attr->set_url('run.php?application=peer_assessment&go=browse');
        $publication_attr->set_publication_object_id($record[PeerAssessmentPublication :: PROPERTY_CONTENT_OBJECT]);

        $res->free();
        
        return $publication_attr;
    }

	function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if(!$object_id)
        {
    		$condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_PUBLISHER, $user->get_id());
        }
        else
        {
        	$condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        }
        return $this->database->count_objects(PeerAssessmentPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        $publications = $this->retrieve_peer_assessment_publications($condition);

        $succes = true;

        while ($publication = $publications->next_result())
        {
            $succes &= $publication->delete();
        }

        return $succes;
    }
    
	function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(PeerAssessmentPublication :: PROPERTY_ID, $publication_id);
        return $this->database->delete(PeerAssessmentPublication :: get_table_name(), $condition);
    }

    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name(PeerAssessmentPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name(PeerAssessmentPublication :: PROPERTY_CONTENT_OBJECT)] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(PeerAssessmentPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
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
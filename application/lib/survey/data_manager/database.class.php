<?php
/**
 * $Id: database.class.php 237 2009-11-16 13:04:53Z vanpouckesven $
 * @package application.lib.survey.data_manager
 */
require_once dirname(__FILE__) . '/../survey_publication.class.php';
require_once dirname(__FILE__) . '/../category_manager/survey_publication_category.class.php';
require_once dirname(__FILE__) . '/../survey_publication_group.class.php';
require_once dirname(__FILE__) . '/../survey_publication_user.class.php';
require_once 'MDB2.php';

/**
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Sven Vanpoucke
 * @author
 */

class DatabaseSurveyDataManager extends SurveyDataManager
{
    private $database;

    function initialize()
    {
        $aliases = array();
        //        $aliases[SurveyPublication :: get_table_name()] = 'ason';
        //        $aliases[SurveyPublicationGroup :: get_table_name()] = 'asup';
        //        $aliases[SurveyPublicationUser :: get_table_name()] = 'aser';
        

        $this->database = new Database($aliases);
        $this->database->set_prefix('survey_');
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function create_survey_publication($survey_publication)
    {
        $succes = $this->database->create($survey_publication);
        
        foreach ($survey_publication->get_target_groups() as $group)
        {
            $survey_publication_group = new SurveyPublicationGroup();
            $survey_publication_group->set_survey_publication($survey_publication->get_id());
            $survey_publication_group->set_group_id($group);
            $succes &= $survey_publication_group->create();
        }
        
        foreach ($survey_publication->get_target_users() as $user)
        {
            $survey_publication_user = new SurveyPublicationUser();
            $survey_publication_user->set_survey_publication($survey_publication->get_id());
            $survey_publication_user->set_user($user);
            $succes &= $survey_publication_user->create();
        }
        
        return $succes;
    }

    function update_survey_publication($survey_publication)
    {
        
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_ID, $survey_publication->get_id());
        $succes = $this->database->update($survey_publication, $condition);
        
        // Delete target users and groups
        $condition = new EqualityCondition('survey_publication_id', $survey_publication->get_id());
        $this->database->delete_objects(SurveyPublicationUser :: get_table_name(), $condition);
        $this->database->delete_objects(SurveyPublicationGroup :: get_table_name(), $condition);
        
        // Add updated target users and groups
        foreach ($survey_publication->get_target_groups() as $group)
        {
            $survey_publication_group = new SurveyPublicationGroup();
            $survey_publication_group->set_survey_publication($survey_publication->get_id());
            $survey_publication_group->set_group_id($group);
            $succes &= $survey_publication_group->create();
        }
        
        foreach ($survey_publication->get_target_users() as $user)
        {
            $survey_publication_user = new SurveyPublicationUser();
            $survey_publication_user->set_survey_publication($survey_publication->get_id());
            $survey_publication_user->set_user($user);
            $succes &= $survey_publication_user->create();
        }
        
        return $succes;
    }

    function delete_survey_publication($survey_publication)
    {
        
        $user_condition = new EqualityCondition(SurveyPublicationUser :: PROPERTY_SURVEY_PUBLICATION, $survey_publication->get_id());
        $group_condition = new EqualityCondition(SurveyPublicationGroup :: PROPERTY_SURVEY_PUBLICATION, $survey_publication->get_id());
        $publication_condition = new EqualityCondition(SurveyPublication :: PROPERTY_ID, $survey_publication->get_id());
        
        $this->database->delete_objects(SurveyPublicationUser :: get_table_name(), $user_condition);
        $this->database->delete_objects(SurveyPublicationGroup :: get_table_name(), $group_condition);
        return $this->database->delete($survey_publication->get_table_name(), $publication_condition);
    }

    function count_survey_participant_trackers($condition = null)
    {
        //$database = TrackingDataManager::get_instance()->get_database();
        $dummy = new SurveyParticipantTracker();
        //$table_name = $dummy->get_table_name();
        return $dummy->count_tracker_items($condition);
        //return $database->count_distinct($table_name, SurveyParticipantTracker ::PROPERTY_USER_ID,$condition);
    

    }
	    
    function count_survey_publications($condition = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->database->get_alias(SurveyPublication :: get_table_name());
        $publication_user_alias = $this->database->get_alias(SurveyPublicationUser :: get_table_name());
        $publication_group_alias = $this->database->get_alias(SurveyPublicationGroup :: get_table_name());
        $object_alias = $this->database->get_alias(ContentObject :: get_table_name());
        
        $query = 'SELECT COUNT(DISTINCT ' . $this->database->escape_column_name(SurveyPublication :: PROPERTY_ID, $publication_alias) . ') FROM ' . $this->database->escape_table_name(SurveyPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->database->escape_column_name(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(SurveyPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->database->escape_column_name(SurveyPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(SurveyPublicationUser :: PROPERTY_SURVEY_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(SurveyPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->database->escape_column_name(SurveyPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(SurveyPublicationGroup :: PROPERTY_SURVEY_PUBLICATION, $publication_group_alias);
        
        return $this->database->count_result_set($query, SurveyPublication :: get_table_name(), $condition);
    }

    function create_survey_publication_category($survey_category)
    {
        return $this->database->create($survey_category);
    }

    function update_survey_publication_category($survey_category)
    {
        $condition = new EqualityCondition(SurveyPublicationCategory :: PROPERTY_ID, $survey_category->get_id());
        return $this->database->update($survey_category, $condition);
    }

    function delete_survey_publication_category($survey_category)
    {
        $condition = new EqualityCondition(SurveyPublicationCategory :: PROPERTY_ID, $survey_category->get_id());
        return $this->database->delete($survey_category->get_table_name(), $condition);
    }

    function count_survey_publication_categories($condition = null)
    {
        return $this->database->count_objects(SurveyPublicationCategory :: get_table_name(), $condition);
    }

    function retrieve_survey_publication_category($id)
    {
        $condition = new EqualityCondition(SurveyPublicationCategory :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(SurveyPublicationCategory :: get_table_name(), $condition);
    }

    function retrieve_survey_publication_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(SurveyPublicationCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function select_next_survey_publication_category_display_order($parent)
    {
        $condition = new EqualityCondition(SurveyPublicationCategory :: PROPERTY_PARENT, $parent);
        return $this->database->retrieve_next_sort_value(SurveyPublicationCategory :: get_table_name(), SurveyPublicationCategory :: PROPERTY_DISPLAY_ORDER, $condition);
    }

    function retrieve_survey_publication($id)
    {
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(SurveyPublication :: get_table_name(), $condition, array(), SurveyPublication :: CLASS_NAME);
    }

    function retrieve_survey_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->database->get_alias(SurveyPublication :: get_table_name());
        $publication_user_alias = $this->database->get_alias(SurveyPublicationUser :: get_table_name());
        $publication_group_alias = $this->database->get_alias(SurveyPublicationGroup :: get_table_name());
        $object_alias = $this->database->get_alias(ContentObject :: get_table_name());
        
        $query = 'SELECT  DISTINCT ' . $publication_alias . '.* FROM ' . $this->database->escape_table_name(SurveyPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->database->escape_column_name(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(SurveyPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->database->escape_column_name(SurveyPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(SurveyPublicationUser :: PROPERTY_SURVEY_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(SurveyPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->database->escape_column_name(SurveyPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(SurveyPublicationGroup :: PROPERTY_SURVEY_PUBLICATION, $publication_group_alias);
        
        return $this->database->retrieve_object_set($query, SurveyPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyPublication :: CLASS_NAME);
    }

    function retrieve_survey_participant_trackers($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        //$database = TrackingDataManager::get_instance()->get_database();
        $dummy = new SurveyParticipantTracker();
        //$table_name = $dummy->get_table_name();
        //$result = $database->retrieve_distinct($table_name, SurveyParticipantTracker ::PROPERTY_USER_ID,$condition);
        //$condition = new InCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $result);
        return $dummy->retrieve_tracker_items_result_set($condition);
    
    }

    function create_survey_publication_group($survey_publication_group)
    {
        return $this->database->create($survey_publication_group);
    }

    function update_survey_publication_group($survey_publication_group)
    {
        $condition = new EqualityCondition(SurveyPublicationGroup :: PROPERTY_ID, $survey_publication_group->get_id());
        return $this->database->update($survey_publication_group, $condition);
    }

    function delete_survey_publication_group($survey_publication_group)
    {
        $condition = new EqualityCondition(SurveyPublicationGroup :: PROPERTY_ID, $survey_publication_group->get_id());
        return $this->database->delete($survey_publication_group->get_table_name(), $condition);
    }

    function count_survey_publication_groups($condition = null)
    {
        return $this->database->count_objects(SurveyPublicationGroup :: get_table_name(), $condition);
    }

    function retrieve_survey_publication_group($id)
    {
        $condition = new EqualityCondition(SurveyPublicationGroup :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(SurveyPublicationGroup :: get_table_name(), $condition, array(), SurveyPublicationGroup :: CLASS_NAME);
    }

    function retrieve_survey_publication_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(SurveyPublicationGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyPublicationGroup :: CLASS_NAME);
    }

    function create_survey_publication_user($survey_publication_user)
    {
        return $this->database->create($survey_publication_user);
    }

    function update_survey_publication_user($survey_publication_user)
    {
        $condition = new EqualityCondition(SurveyPublicationUser :: PROPERTY_ID, $survey_publication_user->get_id());
        return $this->database->update($survey_publication_user, $condition);
    }

    function delete_survey_publication_user($survey_publication_user)
    {
        $condition = new EqualityCondition(SurveyPublicationUser :: PROPERTY_ID, $survey_publication_user->get_id());
        return $this->database->delete($survey_publication_user->get_table_name(), $condition);
    }

    function count_survey_publication_users($condition = null)
    {
        return $this->database->count_objects(SurveyPublicationUser :: get_table_name(), $condition);
    }

    function retrieve_survey_publication_user($id)
    {
        $condition = new EqualityCondition(SurveyPublicationUser :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(SurveyPublicationUser :: get_table_name(), $condition, array(), SurveyPublicationUser :: CLASS_NAME);
    }

    function retrieve_survey_publication_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(SurveyPublicationUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyPublicationUser :: CLASS_NAME);
    }

    function create_survey_publication_mail($survey_publication_mail)
    {
        return $this->database->create($survey_publication_mail);
    }

    function update_survey_publication_mail($survey_publication_mail)
    {
        $condition = new EqualityCondition(SurveyPublicationMail :: PROPERTY_ID, $survey_publication_mail->get_id());
        return $this->database->update($survey_publication_mail, $condition);
    }

    function delete_survey_publication_mail($survey_publication_mail)
    {
        $condition = new EqualityCondition(SurveyPublicationMail :: PROPERTY_ID, $survey_publication_mail->get_id());
        return $this->database->delete($survey_publication_mail->get_table_name(), $condition);
    }

    function count_survey_publication_mails($condition = null)
    {
        return $this->database->count_objects(SurveyPublicationMail :: get_table_name(), $condition);
    }

    function retrieve_survey_publication_mail($id)
    {
        $condition = new EqualityCondition(SurveyPublicationMail :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(SurveyPublicationMail :: get_table_name(), $condition, array(), SurveyPublicationMail :: CLASS_NAME);
    }

    function retrieve_survey_publication_mails($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(SurveyPublicationMail :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyPublicationMail :: CLASS_NAME);
    }

    
	function count_survey_pages($survey_ids, $condition = null)
    {
    	return RepositoryDataManager :: get_instance()->count_complex_content_object_items(new InCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey_ids, ComplexContentObjectItem :: get_table_name()));
   }

	function retrieve_survey_pages($survey_ids, $condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
    	$complex_content_objects = RepositoryDataManager :: get_instance()->count_complex_content_object_items(new InCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey_ids, ComplexContentObjectItem :: get_table_name()));

    	$survey_page_ids = array();
    	
        while ($complex_content_object = $complex_content_objects->next_result())
        {
        	 $survey_page_ids[] =  $complex_content_object->get_ref();  
        }
        
        $survey_page_condition = new InCondition(ContentObject:: PROPERTY_ID, $survey_page_ids);
        
        if(isset($condition)){
        	$condition = new AndCondition(array($condition, $survey_page_condition));
        }else{
        	$condition = $survey_page_condition;
        }
          
        return RepositoryDataManager :: get_instance()->retrieve_content_objects($condition, $offset, $max_objects, $order_by);
    }   
    
    
    function content_object_is_published($object_id)
    {
        return $this->any_content_object_is_published(array($object_id));
    }

    function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $object_ids);
        return $this->database->count_objects(SurveyPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_database()->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->database->get_alias(SurveyPublication :: get_table_name());
                
                $query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' . $this->database->escape_table_name(SurveyPublication :: get_table_name()) . ' AS ' . $pub_alias . ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias . ' ON ' . $this->database->escape_column_name(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $pub_alias) . '=' . $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);
                
                $condition = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
                $translator = new ConditionTranslator($this->database);
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
                        $order[] = $this->database->escape_column_name('title') . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    else
                    {
                        $order[] = $this->database->escape_column_name($order_property->get_property()) . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                }
                
                if (count($order) > 0)
                    $query .= ' ORDER BY ' . implode(', ', $order);
            
            }
        }
        else
        {
            $query = 'SELECT * FROM ' . $this->database->escape_table_name(SurveyPublication :: get_table_name());
            $condition = new EqualityCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
            $translator = new ConditionTranslator($this->database);
            $query .= $translator->render_query($condition);
        
        }
        
        $this->database->set_limit($offset, $count);
        $res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[SurveyPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[SurveyPublication :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[SurveyPublication :: PROPERTY_PUBLISHED]);
            $info->set_application('survey');
            //TODO: i8n location string
            $info->set_location(Translation :: get('Survey'));
            $info->set_url('run.php?application=survey&go=browse_surveys');
            $info->set_publication_object_id($record[SurveyPublication :: PROPERTY_CONTENT_OBJECT]);
            
            $publication_attr[] = $info;
        }
        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->database->escape_table_name(SurveyPublication :: get_table_name()) . ' WHERE ' . $this->database->escape_column_name(SurveyPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->database->set_limit(0, 1);
        $res = $this->query($query);
        
        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        
        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[SurveyPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[SurveyPublication :: PROPERTY_PUBLISHER]);
        $publication_attr->set_publication_date($record[SurveyPublication :: PROPERTY_PUBLISHED]);
        $publication_attr->set_application('survey');
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('Survey'));
        $publication_attr->set_url('run.php?application=survey&go=browse_surveys');
        $publication_attr->set_publication_object_id($record[SurveyPublication :: PROPERTY_CONTENT_OBJECT]);
        
        return $publication_attr;
    }

    function count_publication_attributes($type = null, $condition = null)
    {
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
        return $this->database->count_objects(SurveyPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        $publications = $this->retrieve_survey_publications($condition);
        
        $succes = true;
        
        while ($publication = $publications->next_result())
        {
            $succes &= $publication->delete();
        }
        
        return $succes;
    }

    function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_ID, $publication_id);
        return $this->database->delete(SurveyPublication :: get_table_name(), $condition);
    }

    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name(SurveyPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name(SurveyPublication :: PROPERTY_CONTENT_OBJECT)] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(SurveyPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function quote($value)
    {
        return $this->database->quote($value);
    }

    function query($query)
    {
        return $this->database->query($query);
    }
}
?>
<?php

require_once dirname(__FILE__) . '/../survey_publication.class.php';
require_once dirname(__FILE__) . '/../survey_data_manager_interface.class.php';

class DatabaseSurveyDataManager extends Database implements SurveyDataManagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('survey_');
    }

    function create_survey_publication($survey_publication)
    {
        $succes = $this->create($survey_publication);
        return $succes;
    }

    function update_survey_publication($survey_publication)
    {
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_ID, $survey_publication->get_id());
        $succes = $this->update($survey_publication, $condition);
        return $succes;
    }

    function delete_survey_publication($survey_publication)
    {
        $publication_condition = new EqualityCondition(SurveyPublication :: PROPERTY_ID, $survey_publication->get_id());
        return $this->delete($survey_publication->get_table_name(), $publication_condition);
    }

    function count_survey_publications($condition = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->get_alias(SurveyPublication :: get_table_name());
        $object_alias = $rdm->get_alias(ContentObject :: get_table_name());
        $query = 'SELECT COUNT(DISTINCT ' . $this->escape_column_name(SurveyPublication :: PROPERTY_ID, $publication_alias) . ') FROM ' . $this->escape_table_name(SurveyPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->escape_column_name(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        return $this->count_result_set($query, SurveyPublication :: get_table_name(), $condition);
    }

    function retrieve_survey_publication($id)
    {
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_ID, $id);
        return $this->retrieve_object(SurveyPublication :: get_table_name(), $condition, array(), SurveyPublication :: CLASS_NAME);
    }

    function retrieve_survey_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->get_alias(SurveyPublication :: get_table_name());
        $object_alias = $rdm->get_alias(ContentObject :: get_table_name());
        $query = 'SELECT  DISTINCT ' . $publication_alias . '.* FROM ' . $this->escape_table_name(SurveyPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->escape_column_name(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        return $this->retrieve_object_set($query, SurveyPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyPublication :: CLASS_NAME);
    }

    function create_survey_publication_mail($survey_publication_mail)
    {
        return $this->create($survey_publication_mail);
    }

    function update_survey_publication_mail($survey_publication_mail)
    {
        $condition = new EqualityCondition(SurveyPublicationMail :: PROPERTY_ID, $survey_publication_mail->get_id());
        return $this->update($survey_publication_mail, $condition);
    }

    function delete_survey_publication_mail($survey_publication_mail)
    {
        $condition = new EqualityCondition(SurveyPublicationMail :: PROPERTY_ID, $survey_publication_mail->get_id());
        return $this->delete($survey_publication_mail->get_table_name(), $condition);
    }

    function count_survey_publication_mails($condition = null)
    {
        return $this->count_objects(SurveyPublicationMail :: get_table_name(), $condition);
    }

    function retrieve_survey_publication_mail($id)
    {
        $condition = new EqualityCondition(SurveyPublicationMail :: PROPERTY_ID, $id);
        return $this->retrieve_object(SurveyPublicationMail :: get_table_name(), $condition, array(), SurveyPublicationMail :: CLASS_NAME);
    }

    function retrieve_survey_publication_mails($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(SurveyPublicationMail :: get_table_name(), $condition, $offset, $max_objects, $order_by, SurveyPublicationMail :: CLASS_NAME);
    }

    function count_survey_pages($survey_ids, $condition = null)
    {
        
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new InCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey_ids, ComplexContentObjectItem :: get_table_name()));
        
        $survey_page_ids = array();
        
        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $survey_page_ids[] = $complex_content_object->get_ref();
        }
        
        if (count($survey_page_ids) == 0)
        {
            $survey_page_ids[] = 0;
        }
        
        $survey_page_condition = new InCondition(ContentObject :: PROPERTY_ID, $survey_page_ids, ContentObject :: get_table_name());
        
        if (isset($condition))
        {
            $condition = new AndCondition(array($condition, $survey_page_condition));
        }
        else
        {
            $condition = $survey_page_condition;
        }
        
        return RepositoryDataManager :: get_instance()->count_content_objects($condition, $offset, $max_objects, $order_by);
    
    }

    function retrieve_survey_page($page_id)
    {
        return RepositoryDataManager :: get_instance()->retrieve_content_object($page_id, SurveyPage :: get_type_name());
    }

    function retrieve_survey_pages($survey_ids, $condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        
        //test
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new InCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey_ids, ComplexContentObjectItem :: get_table_name()));
        
        $survey_page_ids = array();
        
        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $survey_page_ids[] = $complex_content_object->get_ref();
        }
        
        if (count($survey_page_ids) == 0)
        {
            $survey_page_ids[] = 0;
        }
        
        $survey_page_condition = new InCondition(ContentObject :: PROPERTY_ID, $survey_page_ids, ContentObject :: get_table_name());
        
        if (isset($condition))
        {
            $condition = new AndCondition(array($condition, $survey_page_condition));
        }
        else
        {
            $condition = $survey_page_condition;
        }
        
        return RepositoryDataManager :: get_instance()->retrieve_content_objects($condition, $offset, $max_objects, $order_by);
    }

    function count_survey_questions($page_ids, $condition = null)
    {
        
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new InCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $page_ids, ComplexContentObjectItem :: get_table_name()));
        
        $page_question_ids = array();
        
        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $page_question_ids[] = $complex_content_object->get_ref();
        }
        
        if (count($page_question_ids) == 0)
        {
            $page_question_ids[] = 0;
        }
        
        $page_question_condition = new InCondition(ContentObject :: PROPERTY_ID, $page_question_ids, ContentObject :: get_table_name());
        
        if (isset($condition))
        {
            $condition = new AndCondition(array($condition, $page_question_condition));
        }
        else
        {
            $condition = $page_question_condition;
        }
        
        return RepositoryDataManager :: get_instance()->count_content_objects($condition, $offset, $max_objects, $order_by);
    
    }

    function retrieve_survey_question($question_id)
    {
        return RepositoryDataManager :: get_instance()->retrieve_content_object($question_id);
    }

    function retrieve_survey_questions($page_ids, $condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        
        //test
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new InCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $page_ids, ComplexContentObjectItem :: get_table_name()));
        
        $page_question_ids = array();
        
        while ($complex_content_object = $complex_content_objects->next_result())
        {
            $page_question_ids[] = $complex_content_object->get_ref();
        }
        
        if (count($page_question_ids) == 0)
        {
            $page_question_ids[] = 0;
        }
        
        $page_question_condition = new InCondition(ContentObject :: PROPERTY_ID, $page_question_ids, ContentObject :: get_table_name());
        
        if (isset($condition))
        {
            $condition = new AndCondition(array($condition, $page_question_condition));
        }
        else
        {
            $condition = $page_question_condition;
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
        return $this->count_objects(SurveyPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->get_alias(SurveyPublication :: get_table_name());
                
                $query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' . $this->escape_table_name(SurveyPublication :: get_table_name()) . ' AS ' . $pub_alias . ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias . ' ON ' . $this->escape_column_name(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $pub_alias) . '=' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);
                
                $condition = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
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
            $query = 'SELECT * FROM ' . $this->escape_table_name(SurveyPublication :: get_table_name());
            $condition = new EqualityCondition(SurveyPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        
        }
        
        $this->set_limit($offset, $count);
        $res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[SurveyPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[SurveyPublication :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[SurveyPublication :: PROPERTY_PUBLISHED]);
            $info->set_application(SurveyManager :: APPLICATION_NAME);
            //TODO: i8n location string
            $info->set_location(Translation :: get('Survey'));
            $info->set_url('run.php?application=survey&go=' . SurveyManager :: ACTION_VIEW_SURVEY_PUBLICATION);
            $info->set_publication_object_id($record[SurveyPublication :: PROPERTY_CONTENT_OBJECT]);
            
            $publication_attr[] = $info;
        }
        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name(SurveyPublication :: get_table_name()) . ' WHERE ' . $this->escape_column_name(SurveyPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->set_limit(0, 1);
        $res = $this->query($query);
        
        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        
        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[SurveyPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[SurveyPublication :: PROPERTY_PUBLISHER]);
        $publication_attr->set_publication_date($record[SurveyPublication :: PROPERTY_PUBLISHED]);
        $publication_attr->set_application(SurveyManager :: APPLICATION_NAME);
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('Survey'));
        $publication_attr->set_url('run.php?application=survey&go=browse_surveys');
        $publication_attr->set_publication_object_id($record[SurveyPublication :: PROPERTY_CONTENT_OBJECT]);
        
        return $publication_attr;
    }

    function count_publication_attributes($type = null, $condition = null)
    {
        $condition = new EqualityCondition(SurveyPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
        return $this->count_objects(SurveyPublication :: get_table_name(), $condition);
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
        return $this->delete(SurveyPublication :: get_table_name(), $condition);
    }

    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->escape_column_name(SurveyPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name(SurveyPublication :: PROPERTY_CONTENT_OBJECT)] = $publication_attr->get_publication_object_id();
        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name(SurveyPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
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
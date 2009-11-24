<?php
/**
 * $Id: database.class.php 237 2009-11-16 13:04:53Z vanpouckesven $
 * @package application.lib.assessment.data_manager
 */
require_once dirname(__FILE__) . '/../assessment_publication.class.php';
require_once dirname(__FILE__) . '/../survey_invitation.class.php';
require_once dirname(__FILE__) . '/../category_manager/assessment_publication_category.class.php';
require_once dirname(__FILE__) . '/../assessment_publication_group.class.php';
require_once dirname(__FILE__) . '/../assessment_publication_user.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 *  @author
 */

class DatabaseAssessmentDataManager extends AssessmentDataManager
{
    private $database;

    function initialize()
    {
        $aliases = array();
        $aliases[AssessmentPublication :: get_table_name()] = 'ason';
        $aliases[AssessmentPublicationGroup :: get_table_name()] = 'asup';
        $aliases[AssessmentPublicationUser :: get_table_name()] = 'aser';

        $this->database = new Database($aliases);
        $this->database->set_prefix('assessment_');
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function create_assessment_publication($assessment_publication)
    {
        $succes = $this->database->create($assessment_publication);

        foreach ($assessment_publication->get_target_groups() as $group)
        {
            $assessment_publication_group = new AssessmentPublicationGroup();
            $assessment_publication_group->set_assessment_publication($assessment_publication->get_id());
            $assessment_publication_group->set_group_id($group);
            $succes &= $assessment_publication_group->create();
        }

        foreach ($assessment_publication->get_target_users() as $user)
        {
            $assessment_publication_user = new AssessmentPublicationUser();
            $assessment_publication_user->set_assessment_publication($assessment_publication->get_id());
            $assessment_publication_user->set_user($user);
            $succes &= $assessment_publication_user->create();
        }

        return $succes;
    }

    function update_assessment_publication($assessment_publication)
    {
        $condition = new EqualityCondition(AssessmentPublication :: PROPERTY_ID, $assessment_publication->get_id());
        return $this->database->update($assessment_publication, $condition);
    }

    function delete_assessment_publication($assessment_publication)
    {
        $condition = new EqualityCondition(AssessmentPublication :: PROPERTY_ID, $assessment_publication->get_id());
        return $this->database->delete($assessment_publication->get_table_name(), $condition);
    }

    function count_assessment_publications($condition = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->database->get_alias(AssessmentPublication :: get_table_name());
        $publication_user_alias = $this->database->get_alias(AssessmentPublicationUser :: get_table_name());
        $publication_group_alias = $this->database->get_alias(AssessmentPublicationGroup :: get_table_name());
        $object_alias = $this->database->get_alias(ContentObject :: get_table_name());

        $query = 'SELECT COUNT(*) FROM ' . $this->database->escape_table_name(AssessmentPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->database->escape_column_name(AssessmentPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->get_database()->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(AssessmentPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->database->escape_column_name(AssessmentPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(AssessmentPublicationUser :: PROPERTY_ASSESSMENT_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(AssessmentPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->database->escape_column_name(AssessmentPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(AssessmentPublicationGroup :: PROPERTY_ASSESSMENT_PUBLICATION, $publication_group_alias);

        return $this->database->count_result_set($query, AssessmentPublication :: get_table_name(), $condition);
    }

    function create_assessment_publication_category($assessment_category)
    {
        return $this->database->create($assessment_category);
    }

    function update_assessment_publication_category($assessment_category)
    {
        $condition = new EqualityCondition(AssessmentPublicationCategory :: PROPERTY_ID, $assessment_category->get_id());
        return $this->database->update($assessment_category, $condition);
    }

    function delete_assessment_publication_category($assessment_category)
    {
        $condition = new EqualityCondition(AssessmentPublicationCategory :: PROPERTY_ID, $assessment_category->get_id());
        return $this->database->delete($assessment_category->get_table_name(), $condition);
    }

    function count_assessment_publication_categories($condition = null)
    {
        return $this->database->count_objects(AssessmentPublicationCategory :: get_table_name(), $condition);
    }

    function retrieve_assessment_publication_category($id)
    {
        $condition = new EqualityCondition(AssessmentPublicationCategory :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(AssessmentPublicationCategory :: get_table_name(), $condition);
    }

    function retrieve_assessment_publication_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(AssessmentPublicationCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function select_next_assessment_publication_category_display_order($parent)
    {
        $query = 'SELECT MAX(' . AssessmentPublicationCategory :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . $this->database->escape_table_name('assessment_publication_category');

        $condition = new EqualityCondition(AssessmentPublicationCategory :: PROPERTY_PARENT, $parent);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database, $this->database->get_alias(AssessmentPublicationCategory :: get_table_name()));
            $query .= $translator->render_query($condition);
        }

        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);

        return $record[0] + 1;
    }

    function retrieve_assessment_publication($id)
    {
        $condition = new EqualityCondition(AssessmentPublication :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(AssessmentPublication :: get_table_name(), $condition, array(), AssessmentPublication :: CLASS_NAME);
    }

    function retrieve_assessment_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->database->get_alias(AssessmentPublication :: get_table_name());
        $publication_user_alias = $this->database->get_alias(AssessmentPublicationUser :: get_table_name());
        $publication_group_alias = $this->database->get_alias(AssessmentPublicationGroup :: get_table_name());
        $object_alias = $this->database->get_alias(ContentObject :: get_table_name());

        $query = 'SELECT ' . $publication_alias . '.* FROM ' . $this->database->escape_table_name(AssessmentPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->database->escape_column_name(AssessmentPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->get_database()->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(AssessmentPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->database->escape_column_name(AssessmentPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(AssessmentPublicationUser :: PROPERTY_ASSESSMENT_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name(AssessmentPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->database->escape_column_name(AssessmentPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->database->escape_column_name(AssessmentPublicationGroup :: PROPERTY_ASSESSMENT_PUBLICATION, $publication_group_alias);

        return $this->database->retrieve_object_set($query, AssessmentPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, AssessmentPublication :: CLASS_NAME);
    }

    function create_assessment_publication_group($assessment_publication_group)
    {
        return $this->database->create($assessment_publication_group);
    }

    function update_assessment_publication_group($assessment_publication_group)
    {
        $condition = new EqualityCondition(AssessmentPublicationGroup :: PROPERTY_ID, $assessment_publication_group->get_id());
        return $this->database->update($assessment_publication_group, $condition);
    }

    function delete_assessment_publication_group($assessment_publication_group)
    {
        $condition = new EqualityCondition(AssessmentPublicationGroup :: PROPERTY_ID, $assessment_publication_group->get_id());
        return $this->database->delete($assessment_publication_group->get_table_name(), $condition);
    }

    function count_assessment_publication_groups($condition = null)
    {
        return $this->database->count_objects(AssessmentPublicationGroup :: get_table_name(), $condition);
    }

    function retrieve_assessment_publication_group($id)
    {
        $condition = new EqualityCondition(AssessmentPublicationGroup :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(AssessmentPublicationGroup :: get_table_name(), $condition, array(), AssessmentPublicationGroup :: CLASS_NAME);
    }

    function retrieve_assessment_publication_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(AssessmentPublicationGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, AssessmentPublicationGroup :: CLASS_NAME);
    }

    function create_assessment_publication_user($assessment_publication_user)
    {
        return $this->database->create($assessment_publication_user);
    }

    function update_assessment_publication_user($assessment_publication_user)
    {
        $condition = new EqualityCondition(AssessmentPublicationUser :: PROPERTY_ID, $assessment_publication_user->get_id());
        return $this->database->update($assessment_publication_user, $condition);
    }

    function delete_assessment_publication_user($assessment_publication_user)
    {
        $condition = new EqualityCondition(AssessmentPublicationUser :: PROPERTY_ID, $assessment_publication_user->get_id());
        return $this->database->delete($assessment_publication_user->get_table_name(), $condition);
    }

    function count_assessment_publication_users($condition = null)
    {
        return $this->database->count_objects(AssessmentPublicationUser :: get_table_name(), $condition);
    }

    function retrieve_assessment_publication_user($id)
    {
        $condition = new EqualityCondition(AssessmentPublicationUser :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(AssessmentPublicationUser :: get_table_name(), $condition, array(), AssessmentPublicationUser :: CLASS_NAME);
    }

    function retrieve_assessment_publication_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(AssessmentPublicationUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, AssessmentPublicationUser :: CLASS_NAME);
    }

    function create_survey_invitation($survey_invitation)
    {
        return $this->database->create($survey_invitation);
    }

    function update_survey_invitation($survey_invitation)
    {
        $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $survey_invitation->get_id());
        return $this->database->update($survey_invitation, $condition);
    }

    function delete_survey_invitation($survey_invitation)
    {
        $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $survey_invitation->get_id());
        return $this->database->delete($survey_invitation->get_table_name(), $condition);
    }

    function count_survey_invitations($condition = null)
    {
        return $this->database->count_objects(SurveyInvitation :: get_table_name(), $condition);
    }

    function retrieve_survey_invitation($id)
    {
        $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(SurveyInvitation :: get_table_name(), $condition);
    }

    function retrieve_survey_invitations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->database->retrieve_objects(SurveyInvitation :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function content_object_is_published($object_id)
    {
        return $this->any_content_object_is_published(array($object_id));
    }

    function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(AssessmentPublication :: PROPERTY_CONTENT_OBJECT, $object_ids);
        return $this->database->count_objects(AssessmentPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_database()->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->database->get_alias(AssessmentPublication :: get_table_name());

            	$query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->database->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' .
                		 $this->database->escape_table_name(AssessmentPublication :: get_table_name()) . ' AS ' . $pub_alias .
                		 ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias .
                		 ' ON ' . $this->database->escape_column_name(AssessmentPublication :: PROPERTY_CONTENT_OBJECT, $pub_alias) . '=' .
                		 $this->database->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

                $condition = new EqualityCondition(AssessmentPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
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
                        $order[] = 'co.' . $this->database->escape_column_name('title') . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
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
            $query = 'SELECT * FROM ' . $this->database->escape_table_name(AssessmentPublication :: get_table_name());
           	$condition = new EqualityCondition(AssessmentPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
           	$translator = new ConditionTranslator($this->database);
           	$query .= $translator->render_query($condition);

        }

        $this->database->set_limit($offset, $count);
		$res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[AssessmentPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[AssessmentPublication :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[AssessmentPublication :: PROPERTY_PUBLISHED]);
            $info->set_application('assessment');
            //TODO: i8n location string
            $info->set_location(Translation :: get('Assessment'));
            $info->set_url('run.php?application=assessment&go=browse_assessments');
            $info->set_publication_object_id($record[AssessmentPublication :: PROPERTY_CONTENT_OBJECT]);

            $publication_attr[] = $info;
        }
        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->database->escape_table_name(AssessmentPublication :: get_table_name()) . ' WHERE ' . $this->database->escape_column_name(AssessmentPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->database->set_limit(0, 1);
        $res = $this->query($query);

        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[AssessmentPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[AssessmentPublication :: PROPERTY_PUBLISHER]);
        $publication_attr->set_publication_date($record[AssessmentPublication :: PROPERTY_PUBLISHED]);
        $publication_attr->set_application('assessment');
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('Assessment'));
        $publication_attr->set_url('run.php?application=assessment&go=browse_assessments');
        $publication_attr->set_publication_object_id($record[AssessmentPublication :: PROPERTY_CONTENT_OBJECT]);

        return $publication_attr;
    }

    function count_publication_attributes($type = null, $condition = null)
    {
        $condition = new EqualityCondition(AssessmentPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
        return $this->database->count_objects(AssessmentPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(AssessmentPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        $publications = $this->retrieve_assessment_publications($condition);

        $succes = true;

        while ($publication = $publications->next_result())
        {
            $succes &= $publication->delete();
        }

        return $succes;
    }

    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name(AssessmentPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name(AssessmentPublication :: PROPERTY_CONTENT_OBJECT)] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(AssessmentPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
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
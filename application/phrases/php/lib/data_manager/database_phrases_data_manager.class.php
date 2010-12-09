<?php
namespace application\phrases;

use common\libraries\Database;
use common\libraries\EqualityCondition;
use common\libraries\Translation;
use repository\RepositoryDataManager;
use repository\ContentObject;
use common\libraries\ConditionTranslator;
use common\libraries\InCondition;
use repository\ContentObjectPublicationAttributes;
use common\libraries\Session;
use repository\content_object\adaptive_assessment\AdaptiveAssessment;
/**
 * $Id: database_phrases_data_manager.class.php 237 2009-11-16 13:04:53Z vanpouckesven $
 * @package application.lib.phrases.data_manager
 */
require_once dirname(__FILE__) . '/../phrases_publication.class.php';
require_once dirname(__FILE__) . '/../category_manager/phrases_publication_category.class.php';
require_once dirname(__FILE__) . '/../phrases_publication_group.class.php';
require_once dirname(__FILE__) . '/../phrases_publication_user.class.php';
require_once dirname(__FILE__) . '/../phrases_data_manager_interface.class.php';

/**
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Hans De Bisschop
 * @author
 */

class DatabasePhrasesDataManager extends Database implements PhrasesDataManagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('phrases_');
    }

    function create_phrases_publication($phrases_publication)
    {
        $succes = $this->create($phrases_publication);

        foreach ($phrases_publication->get_target_groups() as $group)
        {
            $phrases_publication_group = new PhrasesPublicationGroup();
            $phrases_publication_group->set_publication($phrases_publication->get_id());
            $phrases_publication_group->set_group_id($group);
            $succes &= $phrases_publication_group->create();
        }

        foreach ($phrases_publication->get_target_users() as $user)
        {
            $phrases_publication_user = new PhrasesPublicationUser();
            $phrases_publication_user->set_publication($phrases_publication->get_id());
            $phrases_publication_user->set_user($user);
            $succes &= $phrases_publication_user->create();
        }

        return $succes;
    }

    function update_phrases_publication($phrases_publication)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_ID, $phrases_publication->get_id());
        return $this->update($phrases_publication, $condition);
    }

    function delete_phrases_publication($phrases_publication)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_ID, $phrases_publication->get_id());
        return $this->delete($phrases_publication->get_table_name(), $condition);
    }

    function count_phrases_publications($condition = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->get_alias(PhrasesPublication :: get_table_name());
        $publication_user_alias = $this->get_alias(PhrasesPublicationUser :: get_table_name());
        $publication_group_alias = $this->get_alias(PhrasesPublicationGroup :: get_table_name());
        $object_alias = $this->get_alias(ContentObject :: get_table_name());

        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name(PhrasesPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(PhrasesPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->escape_column_name(PhrasesPublicationUser :: PROPERTY_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(PhrasesPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->escape_column_name(PhrasesPublicationGroup :: PROPERTY_PUBLICATION, $publication_group_alias);

        return $this->count_result_set($query, PhrasesPublication :: get_table_name(), $condition);
    }

    function create_phrases_publication_category($phrases_category)
    {
        return $this->create($phrases_category);
    }

    function update_phrases_publication_category($phrases_category)
    {
        $condition = new EqualityCondition(PhrasesPublicationCategory :: PROPERTY_ID, $phrases_category->get_id());
        return $this->update($phrases_category, $condition);
    }

    function delete_phrases_publication_category($phrases_category)
    {
        $condition = new EqualityCondition(PhrasesPublicationCategory :: PROPERTY_ID, $phrases_category->get_id());
        return $this->delete($phrases_category->get_table_name(), $condition);
    }

    function count_phrases_publication_categories($condition = null)
    {
        return $this->count_objects(PhrasesPublicationCategory :: get_table_name(), $condition);
    }

    function retrieve_phrases_publication_category($id)
    {
        $condition = new EqualityCondition(PhrasesPublicationCategory :: PROPERTY_ID, $id);
        return $this->retrieve_object(PhrasesPublicationCategory :: get_table_name(), $condition, null, PhrasesPublicationCategory :: CLASS_NAME);
    }

    function retrieve_phrases_publication_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(PhrasesPublicationCategory :: get_table_name(), $condition, $offset, $count, $order_property, PhrasesPublicationCategory :: CLASS_NAME);
    }

    function select_next_phrases_publication_category_display_order($parent)
    {
        $query = 'SELECT MAX(' . PhrasesPublicationCategory :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . $this->escape_table_name(PhrasesPublicationCategory :: get_table_name()) . ' AS ' . $this->get_alias(PhrasesPublicationCategory :: get_table_name());

        $condition = new EqualityCondition(PhrasesPublicationCategory :: PROPERTY_PARENT, $parent);

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $this->get_alias(PhrasesPublicationCategory :: get_table_name()));
            $query .= $translator->render_query($condition);
        }

        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
        $res->free();

        return $record[0] + 1;
    }

    function retrieve_phrases_publication($id)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_ID, $id);
        return $this->retrieve_object(PhrasesPublication :: get_table_name(), $condition, array(), PhrasesPublication :: CLASS_NAME);
    }

    function retrieve_phrases_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->get_alias(PhrasesPublication :: get_table_name());
        $publication_user_alias = $this->get_alias(PhrasesPublicationUser :: get_table_name());
        $publication_group_alias = $this->get_alias(PhrasesPublicationGroup :: get_table_name());
        $object_alias = $rdm->get_alias(ContentObject :: get_table_name());

        $query = 'SELECT ' . $publication_alias . '.* FROM ' . $this->escape_table_name(PhrasesPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_CONTENT_OBJECT, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(PhrasesPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->escape_column_name(PhrasesPublicationUser :: PROPERTY_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(PhrasesPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->escape_column_name(PhrasesPublicationGroup :: PROPERTY_PUBLICATION, $publication_group_alias);

        return $this->retrieve_object_set($query, PhrasesPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, PhrasesPublication :: CLASS_NAME);
    }

    function create_phrases_publication_group($phrases_publication_group)
    {
        return $this->create($phrases_publication_group);
    }

    function update_phrases_publication_group($phrases_publication_group)
    {
        $condition = new EqualityCondition(PhrasesPublicationGroup :: PROPERTY_ID, $phrases_publication_group->get_id());
        return $this->update($phrases_publication_group, $condition);
    }

    function delete_phrases_publication_group($phrases_publication_group)
    {
        $condition = new EqualityCondition(PhrasesPublicationGroup :: PROPERTY_ID, $phrases_publication_group->get_id());
        return $this->delete($phrases_publication_group->get_table_name(), $condition);
    }

    function count_phrases_publication_groups($condition = null)
    {
        return $this->count_objects(PhrasesPublicationGroup :: get_table_name(), $condition);
    }

    function retrieve_phrases_publication_group($id)
    {
        $condition = new EqualityCondition(PhrasesPublicationGroup :: PROPERTY_ID, $id);
        return $this->retrieve_object(PhrasesPublicationGroup :: get_table_name(), $condition, array(), PhrasesPublicationGroup :: CLASS_NAME);
    }

    function retrieve_phrases_publication_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(PhrasesPublicationGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, PhrasesPublicationGroup :: CLASS_NAME);
    }

    function create_phrases_publication_user($phrases_publication_user)
    {
        return $this->create($phrases_publication_user);
    }

    function update_phrases_publication_user($phrases_publication_user)
    {
        $condition = new EqualityCondition(PhrasesPublicationUser :: PROPERTY_ID, $phrases_publication_user->get_id());
        return $this->update($phrases_publication_user, $condition);
    }

    function delete_phrases_publication_user($phrases_publication_user)
    {
        $condition = new EqualityCondition(PhrasesPublicationUser :: PROPERTY_ID, $phrases_publication_user->get_id());
        return $this->delete($phrases_publication_user->get_table_name(), $condition);
    }

    function count_phrases_publication_users($condition = null)
    {
        return $this->count_objects(PhrasesPublicationUser :: get_table_name(), $condition);
    }

    function retrieve_phrases_publication_user($id)
    {
        $condition = new EqualityCondition(PhrasesPublicationUser :: PROPERTY_ID, $id);
        return $this->retrieve_object(PhrasesPublicationUser :: get_table_name(), $condition, array(), PhrasesPublicationUser :: CLASS_NAME);
    }

    function retrieve_phrases_publication_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(PhrasesPublicationUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, PhrasesPublicationUser :: CLASS_NAME);
    }

    function create_survey_invitation($survey_invitation)
    {
        return $this->create($survey_invitation);
    }

    function update_survey_invitation($survey_invitation)
    {
        $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $survey_invitation->get_id());
        return $this->update($survey_invitation, $condition);
    }

    function delete_survey_invitation($survey_invitation)
    {
        $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $survey_invitation->get_id());
        return $this->delete($survey_invitation->get_table_name(), $condition);
    }

    function count_survey_invitations($condition = null)
    {
        return $this->count_objects(SurveyInvitation :: get_table_name(), $condition);
    }

    function retrieve_survey_invitation($id)
    {
        $condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $id);
        return $this->retrieve_object(SurveyInvitation :: get_table_name(), $condition, null, SurveyInvitation :: CLASS_NAME);
    }

    function retrieve_survey_invitations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(SurveyInvitation :: get_table_name(), $condition, $offset, $count, $order_property, SurveyInvitation :: CLASS_NAME);
    }

    function content_object_is_published($object_id)
    {
        return $this->any_content_object_is_published(array($object_id));
    }

    function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT, $object_ids);
        return $this->count_objects(PhrasesPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->get_alias(PhrasesPublication :: get_table_name());

                $query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' . $this->escape_table_name(PhrasesPublication :: get_table_name()) . ' AS ' . $pub_alias . ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias . ' ON ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_CONTENT_OBJECT, $pub_alias) . '=' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

                $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
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
            $query = 'SELECT * FROM ' . $this->escape_table_name(PhrasesPublication :: get_table_name());
            $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);

        }

        $this->set_limit($offset, $count);
        $res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[PhrasesPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[PhrasesPublication :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[PhrasesPublication :: PROPERTY_PUBLISHED]);
            $info->set_application(PhrasesManager :: APPLICATION_NAME);
            //TODO: i8n location string
            $info->set_location(Translation :: get('TypeName', null, ContentObject :: get_content_object_type_namespace(AdaptiveAssessment :: get_type_name())));
            $info->set_url('run.php?application=phrases&go=' . PhrasesManager :: ACTION_VIEW_PHRASES_PUBLICATION . '&' . PhrasesManager :: PARAM_PHRASES_PUBLICATION . '=' . $info->get_id());
            $info->set_publication_object_id($record[PhrasesPublication :: PROPERTY_CONTENT_OBJECT]);

            $publication_attr[] = $info;
        }

        $res->free();

        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name(PhrasesPublication :: get_table_name()) . ' WHERE ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->set_limit(0, 1);
        $res = $this->query($query);

        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[PhrasesPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[PhrasesPublication :: PROPERTY_PUBLISHER]);
        $publication_attr->set_publication_date($record[PhrasesPublication :: PROPERTY_PUBLISHED]);
        $publication_attr->set_application(PhrasesManager :: APPLICATION_NAME);
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('TypeName', null, ContentObject :: get_content_object_type_namespace(AdaptiveAssessment :: get_type_name())));
        $publication_attr->set_url('run.php?application=phrases&go=' . PhrasesManager :: ACTION_VIEW_PHRASES_PUBLICATION . '&' . PhrasesManager :: PARAM_PHRASES_PUBLICATION . '=' . $info->get_id());
        $publication_attr->set_publication_object_id($record[PhrasesPublication :: PROPERTY_CONTENT_OBJECT]);

        $res->free();

        return $publication_attr;
    }

    function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if (! $object_id)
        {
            $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_PUBLISHER, $user->get_id());
        }
        else
        {
            $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        }
        return $this->count_objects(PhrasesPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        $publications = $this->retrieve_phrases_publications($condition);

        $succes = true;

        while ($publication = $publications->next_result())
        {
            $succes &= $publication->delete();
        }

        return $succes;
    }

    function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_ID, $publication_id);
        return $this->delete(PhrasesPublication :: get_table_name(), $condition);
    }

    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->escape_column_name(PhrasesPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name(PhrasesPublication :: PROPERTY_CONTENT_OBJECT)] = $publication_attr->get_publication_object_id();
        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name(PhrasesPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
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
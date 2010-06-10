<?php
/**
 * $Id: database_phrases_data_manager.class.php 238 2009-11-16 14:10:27Z vanpouckesven $
 * @package application.personal_calendar.data_manager
 */
require_once dirname(__FILE__) . '/../phrases_data_manager.class.php';
require_once dirname(__FILE__) . '/../phrases_publication.class.php';
require_once dirname(__FILE__) . '/../phrases_mastery_level.class.php';
require_once dirname(__FILE__) . '/../phrases_data_manager_interface.class.php';
/**
 * This is an implementation of a personal calendar datamanager using the PEAR::
 * MDB2 package as a database abstraction layer.
 */
class DatabasePhrasesDatamanager extends Database implements PhrasesDataManagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('phrases_');
    }

    public function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        return $this->count_objects(PhrasesPublication :: get_table_name(), $condition) >= 1;
    }

    public function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_ids);
        return $this->count_objects(PhrasesPublication :: get_table_name(), $condition) >= 1;
    }

    /**
     * @see Application::get_content_object_publication_attributes()
     */
    public function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->get_alias(PhrasesPublication :: get_table_name());

                $query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' . $this->escape_table_name(PhrasesPublication :: get_table_name()) . ' AS ' . $pub_alias . ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias . ' ON ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID, $pub_alias) . '=' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

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
            $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
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
            $info->set_location(Utilities :: underscores_to_camelcase_with_spaces(PhrasesManager :: APPLICATION_NAME));
            //TODO: set correct URL
            $info->set_url('run.php?application=phrases&amp;go=view&phrases=' . $info->get_id());
            $info->set_publication_object_id($record[PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID]);
            $publication_attr[] = $info;
        }

        $res->free();

        return $publication_attr;
    }

    /**
     * @see Application::get_content_object_publication_attribute()
     */
    public function get_content_object_publication_attribute($publication_id)
    {
        $record = $this->retrieve_phrases_publication($publication_id);

        $info = new ContentObjectPublicationAttributes();
        $info->set_id($record->get_id());
        $info->set_publisher_user_id($record->get_publisher());
        $info->set_publication_date($record->get_publication_date());
        $info->set_application(PhrasesManager :: APPLICATION_NAME);
        //TODO: i8n location string
        $info->set_location(Utilities :: underscores_to_camelcase_with_spaces(PhrasesManager :: APPLICATION_NAME));
        //TODO: set correct URL
        $info->set_url('run.php?application=phrases&amp;go=view&phrases=' . $info->get_id());
        $info->set_publication_object_id($record->get_content_object());
        return $info;
    }

    function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if (! $object_id)
        {
            $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_PUBLISHER, $user->get_id());
        }
        else
        {
            $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        }
        return $this->count_objects(PhrasesPublication :: get_table_name(), $condition);
    }

    /**
     * @see Application::delete_content_object_publications()
     */
    public function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        return $this->delete(PhrasesPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_ID, $publication_id);
        return $this->delete(PhrasesPublication :: get_table_name(), $condition);
    }

    /**
     * @see Application::update_content_object_publication_id()
     */
    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->escape_column_name('id') . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name(PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID)] = $publication_attr->get_publication_object_id();
        $this->get_connection()->loadModule('Extended');
        return $this->get_connection()->extended->autoExecute($this->get_table_name(PhrasesPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where);
    }

    //Inherited
    function retrieve_phrases_publication($id)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_ID, $id);
        return $this->retrieve_object(PhrasesPublication :: get_table_name(), $condition, array(), PhrasesPublication :: CLASS_NAME);
    }

    //Inherited.
    function retrieve_phrases_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->get_alias(PhrasesPublication :: get_table_name());
        $object_alias = $rdm->get_alias(ContentObject :: get_table_name());
        $mastery_level_alias = $this->get_alias(PhrasesMasteryLevel :: get_table_name());

        $query = 'SELECT ' . $publication_alias . '.* FROM ' . $this->escape_table_name(PhrasesPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
//        $query .= ' JOIN ' . $this->escape_table_name(PhrasesMasteryLevel :: get_table_name()) . ' AS ' . $mastery_level_alias . ' ON ' . $this->escape_column_name(PhrasesPublication :: PROPERTY_MASTERY_LEVEL_ID, $publication_alias) . ' = ' . $this->escape_column_name(PhrasesMasteryLevel :: PROPERTY_ID, $mastery_level_alias);

        return $this->retrieve_object_set($query, PhrasesPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, PhrasesPublication :: CLASS_NAME);
    }

    function count_phrases_publications($condition)
    {
        return $this->count_objects(PhrasesPublication :: get_table_name(), $condition);
    }

    function retrieve_phrases_mastery_level($id)
    {
        $condition = new EqualityCondition(PhrasesMasteryLevel :: PROPERTY_ID, $id);
        return $this->retrieve_object(PhrasesMasteryLevel :: get_table_name(), $condition, array(), PhrasesMasteryLevel :: CLASS_NAME);
    }

    function retrieve_phrases_mastery_levels($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->retrieve_objects(PhrasesMasteryLevel :: get_table_name(), $condition, $offset, $max_objects, $order_by, PhrasesMasteryLevel :: CLASS_NAME);
    }

    function count_phrases_mastery_levels($condition)
    {
        return $this->count_objects(PhrasesMasteryLevel :: get_table_name(), $condition);
    }

    function retrieve_shared_phrases_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $query = 'SELECT DISTINCT ' . $this->get_alias(PhrasesPublication :: get_table_name()) . '.* FROM ' . $this->escape_table_name(PhrasesPublication :: get_table_name()) . ' AS ' . $this->get_alias(PhrasesPublication :: get_table_name());

        return $this->retrieve_object_set($query, PhrasesPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, PhrasesPublication :: CLASS_NAME);
    }

    //Inherited.
    function update_phrases_publication($calendar_event_publication)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_ID, $calendar_event_publication->get_id());
        return $this->update($calendar_event_publication, $condition);
    }

    //Inherited
    function delete_phrases_publication($calendar_event_publication)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_ID, $calendar_event_publication->get_id());
        return $this->delete(PhrasesPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function delete_phrases_publications($object_id)
    {
        $condition = new EqualityCondition(PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        return $this->delete_objects(PhrasesPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function update_phrases_publication_id($publication_attr)
    {
        $where = $this->escape_column_name(PhrasesPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name(PhrasesPublication :: PROPERTY_CONTENT_OBJECT_ID)] = $publication_attr->get_publication_object_id();
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

    function create_phrases_publication($publication)
    {
        return $this->create($publication);
    }

    function create_phrases_mastery_level($mastery_level)
    {
        return $this->create($mastery_level);
    }

    function delete_phrases_mastery_level($mastery_level)
    {
        $condition = new EqualityCondition(PhrasesMasteryLevel :: PROPERTY_ID, $mastery_level->get_id());
        return $this->delete(PhrasesMasteryLevel :: get_table_name(), $condition);
    }

    function update_phrases_mastery_level($mastery_level)
    {
        $condition = new EqualityCondition(PhrasesMasteryLevel :: PROPERTY_ID, $mastery_level->get_id());
        return $this->update($mastery_level, $condition);
    }

    function get_next_mastery_level_display_order_index()
    {
        return $this->retrieve_next_sort_value(PhrasesMasteryLevel :: get_table_name(), PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER);
    }

    function move_phrases_mastery_level($mastery_level, $places)
    {
        if ($places < 0)
        {
            return $this->move_phrases_mastery_level_up($mastery_level, - $places);
        }
        else
        {
            return $this->move_phrases_mastery_level_down($mastery_level, $places);
        }
    }


    private function move_phrases_mastery_level_up($mastery_level, $places)
    {
        $old_index = $mastery_level->get_display_order();
        $condition = new InequalityCondition(PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: LESS_THAN, $old_index);

        $properties[PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER] = $this->escape_column_name(PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER) . '+1';

        if (! $this->update_objects(PhrasesMasteryLevel :: get_table_name(), $properties, $condition, null, $places, new ObjectTableOrder(PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER, SORT_DESC)))
        {
            return false;
        }

        $condition = new EqualityCondition(PROPERTY_DISPLAY_ORDER :: PROPERTY_ID, $mastery_level->get_id());
        $properties[PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER] = $old_index - $places;
        return $this->update_objects(PhrasesMasteryLevel :: get_table_name(), $properties, $condition, null, 1);
    }

    private function move_phrases_mastery_level_down($mastery_level, $places)
    {
        $old_index = $mastery_level->get_display_order();
        $condition = new InequalityCondition(PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER, InequalityCondition :: GREATER_THAN, $old_index);

        $properties[PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER] = $this->escape_column_name(PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER) . '-1';

        if (! $this->update_objects(PhrasesMasteryLevel :: get_table_name(), $properties, $condition, null, $places, new ObjectTableOrder(PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER, SORT_ASC)))
        {
            return false;
        }

        $condition = new EqualityCondition(PhrasesMasteryLevel :: PROPERTY_ID, $mastery_level->get_id());
        $properties[PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER] = $old_index + $places;
        return $this->update_objects(PhrasesMasteryLevel :: get_table_name(), $properties, $condition, null, 1);
    }
}
?>
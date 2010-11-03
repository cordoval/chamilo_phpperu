<?php

namespace application\profiler;

use common\libraries\Path;
use common\libraries\EqualityCondition;
use user\UserDataManager;
use common\libraries\ConditionTranslator;
use common\libraries\ObjectResultSet;
use common\libraries\InCondition;
use repository\RepositoryDataManager;
use repository\ContentObject;
use repository\ContentObjectPublicationAttributes;
use common\libraries\Translation;
use common\libraries\Database;
use user\User;


/**
 * $Id: database_profiler_data_manager.class.php 238 2009-11-16 14:10:27Z vanpouckesven $
 * @package application.profiler.data_manager
 */
require_once Path :: get_repository_path() . 'lib/data_manager/database_repository_data_manager.class.php';

class DatabaseProfilerDataManager extends Database implements ProfilerDataManagerInterface
{
    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('profiler_');
    }

    //Inherited.
    function count_profile_publications($condition = null)
    {
        return $this->count_objects(ProfilerPublication :: get_table_name(), $condition);
    }

    //Inherited
    function retrieve_profile_publication($id)
    {
        $condition = new EqualityCondition(ProfilerPublication :: PROPERTY_ID, $id);
        return $this->retrieve_object(ProfilerPublication :: get_table_name(), $condition, array(), ProfilerPublication :: CLASS_NAME);
    }

    //Inherited.
    function retrieve_profile_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $udm = UserDataManager :: get_instance();
        $publication_alias = $this->get_alias(ProfilerPublication :: get_table_name());

        $query = 'SELECT ' . $publication_alias . '.* FROM ' . $this->escape_table_name(ProfilerPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $udm->escape_table_name(User :: get_table_name()) . ' AS ' . $udm->get_alias(User :: get_table_name());
        $query .= ' ON ' . $this->escape_column_name(ProfilerPublication :: PROPERTY_PUBLISHER, $publication_alias) . ' = ';
        $query .= $udm->escape_column_name(User :: PROPERTY_ID, $udm->get_alias(User :: get_table_name()));

        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $publication_alias);
            $query .= $translator->render_query($condition);
        }

        $orders = array();

        foreach ($order_by as $order)
        {
            $orders[] = $this->escape_column_name($order->get_property(), ($order->alias_is_set() ? $order->get_alias() : $publication_alias)) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }
        if ($max_objects < 0)
        {
            $max_objects = null;
        }

        $this->set_limit(intval($max_objects), intval($offset));
        $res = $this->query($query);

        return new ObjectResultSet($this, $res, ProfilerPublication :: CLASS_NAME);
    }

    //Inherited.
    function update_profile_publication($profile_publication)
    {
        $condition = new EqualityCondition(ProfilerPublication :: PROPERTY_ID, $profile_publication->get_id());
        return $this->update($profile_publication, $condition);
    }

    //Inherited
    function delete_profiler_publication($profile_publication)
    {
        $condition = new EqualityCondition(ProfilerPublication :: PROPERTY_ID, $profile_publication->get_id());
        return $this->delete(ProfilerPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function delete_profile_publications($object_id)
    {
        $condition = new EqualityCondition(ProfilerPublication :: PROPERTY_PROFILE, $object_id);
        return $this->delete_objects(ProfilerPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function update_profile_publication_id($publication_attr)
    {
        $where = $this->escape_column_name(ProfilerPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name(ProfilerPublication :: PROPERTY_PROFILE)] = $publication_attr->get_publication_object_id();
        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name(ProfilerPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    //Inherited.
    function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(ProfilerPublication :: PROPERTY_PROFILE, $object_ids);
        return $this->count_objects(ProfilerPublication :: get_table_name(), $condition) >= 1;
    }

    //Inherited.
    function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(ProfilerPublication :: PROPERTY_PROFILE, $object_id);
        return $this->count_objects(ProfilerPublication :: get_table_name(), $condition) >= 1;
    }

    //Inherited
    function get_content_object_publication_attributes($user_id, $object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->get_alias(ProfilerPublication :: get_table_name());

            	$query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' .
                		 $this->escape_table_name(ProfilerPublication :: get_table_name()) . ' AS ' . $pub_alias .
                		 ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias .
                		 ' ON ' . $this->escape_column_name(ProfilerPublication :: PROPERTY_PROFILE, $pub_alias) . '=' .
                		 $this->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

                $condition = new EqualityCondition(ProfilerPublication :: PROPERTY_PUBLISHER, $user_id);
                $translator = new ConditionTranslator($this);
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
                        $order[] = $this->escape_column_name('title') . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    else
                    {
                        $order[] = $this->escape_column_name($order_property->get_property()) . ' ' . ($order_property->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
                    }
                }

                if(count($order) > 0)
                	$query .= ' ORDER BY ' . implode(', ', $order);

            }
        }
        else
        {
             $query = 'SELECT * FROM ' . $this->escape_table_name(ProfilerPublication :: get_table_name());
           	$condition = new EqualityCondition(ProfilerPublication :: PROPERTY_PROFILE, $object_id);
           	$translator = new ConditionTranslator($this);
           	$query .= $translator->render_query($condition);
        }

        $this->set_limit($offset, $count);
		$res = $this->query($query);

        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $publication = $this->record_to_object($record, ProfilerPublication :: CLASS_NAME);

            $info = new ContentObjectPublicationAttributes();
            $info->set_id($publication->get_id());
            $info->set_publisher_user_id($publication->get_publisher());
            $info->set_publication_date($publication->get_published());
            $info->set_application('Profiler');
            //TODO: i8n location string
            $info->set_location(Translation :: get('List'));
        	$info->set_url('run.php?application=profiler&amp;go='.ProfilerManager::ACTION_VIEW_PUBLICATION.'&profile=' . $publication->get_id());
            $info->set_publication_object_id($publication->get_profile());

            $publication_attr[] = $info;
        }

        $res->free();

        return $publication_attr;
    }

    //Indered.
    function get_content_object_publication_attribute($publication_id)
    {
        $publication = $this->retrieve_profile_publication($publication_id);

        $info = new ContentObjectPublicationAttributes();
        $info->set_id($publication->get_id());
        $info->set_publisher_user_id($publication->get_publisher());
        $info->set_publication_date($publication->get_published());
        $info->set_application('Profiler');
        //TODO: i8n location string
        $info->set_location(Translation :: get('List'));
        $info->set_url('run.php?application=profiler&amp;go=view&profile=' . $publication->get_id());
        $info->set_publication_object_id($publication->get_profile());

        return $info;
    }

	function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if(!$object_id)
        {
    		$condition = new EqualityCondition(ProfilerPublication :: PROPERTY_PUBLISHER, $user->get_id());
        }
        else
        {
        	$condition = new EqualityCondition(ProfilerPublication :: PROPERTY_PROFILE, $object_id);
        }
        return $this->count_objects(ProfilerPublication :: get_table_name(), $condition);
    }

	function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(ProfilerPublication :: PROPERTY_ID, $publication_id);
        return $this->delete(ProfilerPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function create_profile_publication($publication)
    {
        return $this->create($publication);
    }

    function delete_category($category)
    {
        $condition = new EqualityCondition(ProfilerCategory :: PROPERTY_ID, $category->get_id());
        $succes = $this->delete('profiler_category', $condition);

        $query = 'UPDATE ' . $this->escape_table_name('profiler_category') . ' SET ' . $this->escape_column_name(ProfilerCategory :: PROPERTY_DISPLAY_ORDER) . '=' . $this->escape_column_name(ProfilerCategory :: PROPERTY_DISPLAY_ORDER) . '-1 WHERE ' . $this->escape_column_name(ProfilerCategory :: PROPERTY_DISPLAY_ORDER) . '>' .
        	     $this->quote($category->get_display_order()) . ' AND ' . $this->escape_column_name(ProfilerCategory :: PROPERTY_PARENT) . '=' . $this->quote($category->get_parent());

		$res = $this->query($query);
		$res->free();
       	return $succes;
    }

    function update_category($category)
    {
        $condition = new EqualityCondition(ProfilerCategory :: PROPERTY_ID, $category->get_id());
        return $this->update($category, $condition);
    }

    function create_category($category)
    {
        return $this->create($category);
    }

    function count_categories($conditions = null)
    {
        return $this->count_objects(ProfilerCategory :: get_table_name(), $conditions);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(ProfilerCategory :: get_table_name(), $condition, $offset, $count, $order_property, ProfilerCategory::CLASS_NAME);
    }

    function select_next_category_display_order($parent_category_id)
    {
        $query = 'SELECT MAX(' . ProfilerCategory :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . $this->escape_table_name(ProfilerCategory::get_table_name());

        $condition = new EqualityCondition(ProfilerCategory :: PROPERTY_PARENT, $parent_category_id);
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        }

        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();

        return $record[0] + 1;
    }
}
?>
<?php
/**
 * $Id: database_forum_data_manager.class.php 238 2009-11-16 14:10:27Z vanpouckesven $
 * @package application.lib.forum.data_manager
 */
require_once dirname(__FILE__) . '/../forum_publication.class.php';
require_once dirname(__FILE__) . '/../category_manager/forum_publication_category.class.php';
require_once dirname(__FILE__) . '/../forum_data_manager_interface.class.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke & Michael Kyndt
 */

class DatabaseForumDataManager extends Database implements ForumDataManagerInterface
{
    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('forum_');
    }

    function create_forum_publication($forum_publication)
    {
        return $this->create($forum_publication);
    }

    function update_forum_publication($forum_publication)
    {
        $condition = new EqualityCondition(ForumPublication :: PROPERTY_ID, $forum_publication->get_id());
        return $this->update($forum_publication, $condition);
    }

    function delete_forum_publication($forum_publication)
    {
        $condition = new EqualityCondition(ForumPublication :: PROPERTY_ID, $forum_publication->get_id());
        return $this->delete($forum_publication->get_table_name(), $condition);
    }

    function count_forum_publications($condition = null)
    {
        return $this->count_objects(ForumPublication :: get_table_name(), $condition);
    }

    function retrieve_forum_publication($id)
    {
        $condition = new EqualityCondition(ForumPublication :: PROPERTY_ID, $id);
        return $this->retrieve_object(ForumPublication :: get_table_name(), $condition);
    }

    function retrieve_forum_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(ForumPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function move_forum_publication($publication, $places)
    {
        $oldIndex = $publication->get_display_order();
        $newIndex = $oldIndex + $places;

        $publications = $this->retrieve_forum_publications();

        while ($pub = $publications->next_result())
        {
            $index = $pub->get_display_order();

            if ($index == $newIndex)
                $pub->set_display_order($index - $places);

            $pub->update();
        }

        $publication->set_display_order($newIndex);
        $publication->update();
    }

    function create_forum_publication_category($forum_publication_category)
    {
        return $this->create($forum_publication_category);
    }

    function update_forum_publication_category($forum_publication_category)
    {
        $condition = new EqualityCondition(ForumPublicationCategory :: PROPERTY_ID, $forum_publication_category->get_id());
        return $this->update($forum_publication_category, $condition);
    }

    function delete_forum_publication_category($forum_publication_category)
    {
        $condition = new EqualityCondition(ForumPublicationCategory :: PROPERTY_ID, $forum_publication_category->get_id());
        return $this->delete($forum_publication_category->get_table_name(), $condition);
    }

    function count_forum_publication_categories($conditions = null)
    {
        return $this->count_objects(ForumPublicationCategory :: get_table_name(), $conditions);
    }

    function retrieve_forum_publication_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(ForumPublicationCategory :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function get_next_publication_display_order($parent_id)
    {
        $condition = new EqualityCondition(ForumPublication :: PROPERTY_CATEGORY_ID, $parent_id);
        return $this->retrieve_next_sort_value(ForumPublication :: get_table_name(), ForumPublication :: PROPERTY_DISPLAY_ORDER, $condition);
    }

    function retrieve_max_sort_value($table_name, $column, $condition)
    {
        return $this->retrieve_max_sort_value($table_name, $column, $condition);
    }

	//Publication attributes

	function content_object_is_published($object_id)
    {
        return $this->any_content_object_is_published(array($object_id));
    }

    function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(ForumPublication :: PROPERTY_FORUM_ID, $object_ids);
        return $this->count_objects(ForumPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $rdm = RepositoryDataManager :: get_instance();
                $co_alias = $rdm->get_database()->get_alias(ContentObject :: get_table_name());
                $pub_alias = $this->get_alias(ForumPublication :: get_table_name());

            	$query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' .
                		 $this->escape_table_name(ForumPublication :: get_table_name()) . ' AS ' . $pub_alias .
                		 ' JOIN ' . $rdm->get_database()->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias .
                		 ' ON ' . $this->escape_column_name(ForumPublication :: PROPERTY_FORUM_ID, $pub_alias) . '=' .
                		 $this->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

                $condition = new EqualityCondition(ForumPublication :: PROPERTY_AUTHOR, Session :: get_user_id());
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
            $query = 'SELECT * FROM ' . $this->escape_table_name(ForumPublication :: get_table_name());
           	$condition = new EqualityCondition(ForumPublication :: PROPERTY_FORUM_ID, $object_id);
           	$translator = new ConditionTranslator($this);
           	$query .= $translator->render_query($condition);

        }

        $this->set_limit($offset, $count);
		$res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[ForumPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[ForumPublication :: PROPERTY_AUTHOR]);
            $info->set_application('alexia');
            //TODO: i8n location string
            $info->set_location(Translation :: get('Forum'));
            $info->set_url('run.php?application=alexia&go=browse');
            $info->set_publication_object_id($record[ForumPublication :: PROPERTY_FORUM_ID]);

            $publication_attr[] = $info;
        }

        $res->free();

        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name(ForumPublication :: get_table_name()) . ' WHERE ' . $this->escape_column_name(ForumPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->set_limit(0, 1);
        $res = $this->query($query);
        if($res->numRows() == 0)
        {
        	return null;
        }
        
        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

        $res->free();
        
        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[ForumPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[ForumPublication :: PROPERTY_AUTHOR]);
        $publication_attr->set_application('forum');
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('Forum'));
        $publication_attr->set_url('run.php?application=forum&go=browse');
        $publication_attr->set_publication_object_id($record[ForumPublication :: PROPERTY_FORUM_ID]);

        return $publication_attr;
    }

	function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if(!$object_id)
        {
    		$condition = new EqualityCondition(ForumPublication :: PROPERTY_AUTHOR, $user->get_id());
        }
        else
        {
        	$condition = new EqualityCondition(ForumPublication :: PROPERTY_FORUM_ID, $object_id);
        }
        return $this->count_objects(ForumPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(ForumPublication :: PROPERTY_FORUM_ID, $object_id);
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
        $where = $this->escape_column_name(ForumPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name(ForumPublication :: PROPERTY_FORUM_ID)] = $publication_attr->get_publication_object_id();
        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name(ForumPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
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
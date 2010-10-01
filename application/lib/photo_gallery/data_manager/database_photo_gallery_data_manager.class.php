<?php
/**
 * $Id: database_photo_gallery_data_manager.class.php 
 * @package application.lib.photo_gallery.data_manager
 */
require_once dirname(__FILE__) . '/../photo_gallery_publication.class.php';
require_once dirname(__FILE__) . '/../photo_gallery_data_manager_interface.class.php';
require_once dirname(__FILE__) . '/../photo_gallery_publication_user.class.php';
require_once dirname(__FILE__) . '/../photo_gallery_publication_group.class.php';

/**
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Magali Gillard
 */

class DatabasePhotoGalleryDataManager extends Database implements PhotoGalleryDataManagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('photo_gallery_');
    }

    public function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        return $this->count_objects(PhotoGalleryPublication :: get_table_name(), $condition) >= 1;
    }

    public function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(PhotoGalleryPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_ids);
        return $this->count_objects(PhotoGalleryPublication :: get_table_name(), $condition) >= 1;
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
                $pub_alias = $this->get_alias(PhotoGalleryPublication :: get_table_name());
                
                $query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' . $this->escape_table_name(PhotoGalleryPublication :: get_table_name()) . ' AS ' . $pub_alias . ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias . ' ON ' . $this->escape_column_name(PhotoGalleryPublication :: PROPERTY_CONTENT_OBJECT_ID, $pub_alias) . '=' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);
                
                $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
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
            $query = 'SELECT * FROM ' . $this->escape_table_name(PhotoGalleryPublication :: get_table_name());
            $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        }
        
        $this->set_limit($offset, $count);
        $res = $this->query($query);
        
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[PhotoGalleryPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[PhotoGalleryPublication :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[PhotoGalleryPublication :: PROPERTY_PUBLISHED]);
            $info->set_application(PhotoGalleryManager :: APPLICATION_NAME);
            //TODO: i8n location string
            $info->set_location(Utilities :: underscores_to_camelcase_with_spaces(PhotoGalleryManager :: APPLICATION_NAME));
            //TODO: set correct URL
            $info->set_url('run.php?application=photo_gallery&amp;go=' . PhotoGalleryManager :: ACTION_VIEW_PUBLICATION . '&photo_gallery=' . $info->get_id());
            $info->set_publication_object_id($record[PhotoGalleryPublication :: PROPERTY_CONTENT_OBJECT_ID]);
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
        $record = $this->retrieve_photo_gallery_publication($publication_id);
        
        $info = new ContentObjectPublicationAttributes();
        $info->set_id($record->get_id());
        $info->set_publisher_user_id($record->get_publisher());
        $info->set_publication_date($record->get_publication_date());
        $info->set_application(PhotoGalleryManager :: APPLICATION_NAME);
        //TODO: i8n location string
        $info->set_location(Utilities :: underscores_to_camelcase_with_spaces(PhotoGalleryManager :: APPLICATION_NAME));
        //TODO: set correct URL
        $info->set_url('run.php?application=photo_gallery&amp;go=view&photo_gallery=' . $info->get_id());
        $info->set_publication_object_id($record->get_content_object());
        return $info;
    }

    function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if (! $object_id)
        {
            $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_PUBLISHER, $user->get_id());
        }
        else
        {
            $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        }
        return $this->count_objects(PhotoGalleryPublication :: get_table_name(), $condition);
    }

    function count_photo_gallery_publications($condition = null)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->get_alias(PhotoGalleryPublication :: get_table_name());
        $publication_user_alias = $this->get_alias(PhotoGalleryPublicationUser :: get_table_name());
        $publication_group_alias = $this->get_alias(PhotoGalleryPublicationGroup :: get_table_name());
        $object_alias = $this->get_alias(ContentObject :: get_table_name());
        
        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name(PhotoGalleryPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->escape_column_name(PhotoGalleryPublication :: PROPERTY_CONTENT_OBJECT_ID, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(PhotoGalleryPublicationUser :: get_table_name()) . ' AS ' . $publication_user_alias . ' ON ' . $this->escape_column_name(PhotoGalleryPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->escape_column_name(PhotoGalleryPublicationUser :: PROPERTY_PUBLICATION, $publication_user_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name(PhotoGalleryPublicationGroup :: get_table_name()) . ' AS ' . $publication_group_alias . ' ON ' . $this->escape_column_name(PhotoGalleryPublication :: PROPERTY_ID, $publication_alias) . '  = ' . $this->escape_column_name(PhotoGalleryPublicationGroup :: PROPERTY_PUBLICATION, $publication_group_alias);
        
        return $this->count_result_set($query, PhotoGalleryPublication :: get_table_name(), $condition);
    }

    /**
     * @see Application::delete_content_object_publications()
     */
    public function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        return $this->delete(PhotoGalleryPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_ID, $publication_id);
        return $this->delete(PhotoGalleryPublication :: get_table_name(), $condition);
    }

    /**
     * @see Application::update_content_object_publication_id()
     */
    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->escape_column_name('id') . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name('content_object')] = $publication_attr->get_publication_object_id();
        $this->get_connection()->loadModule('Extended');
        return $this->get_connection()->extended->autoExecute($this->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where);
    }

    //Inherited
    function retrieve_photo_gallery_publication($id)
    {
        $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_ID, $id);
        return $this->retrieve_object(PhotoGalleryPublication :: get_table_name(), $condition, array(), PhotoGalleryPublication :: CLASS_NAME);
    }

    //Inherited.
    function retrieve_photo_gallery_publications($condition = null, $offset = 0, $max_objects = -1, $order_by = array ())
    {        
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->get_alias(PhotoGalleryPublication :: get_table_name());
        $object_alias = RepositoryDataManager :: get_instance()->get_alias(ContentObject :: get_table_name());
        
        $query = 'SELECT DISTINCT ' . $publication_alias . '.* FROM ' . $this->escape_table_name(PhotoGalleryPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $object_alias . ' ON ' . $this->escape_column_name(PhotoGalleryPublication :: PROPERTY_CONTENT_OBJECT_ID, $publication_alias) . ' = ' . $rdm->escape_column_name(ContentObject :: PROPERTY_ID, $object_alias);
        $query .= ' LEFT JOIN ' . $this->escape_table_name('publication_user') . ' AS ' . $this->get_alias('publication_user') . ' ON ' . $this->get_alias(PhotoGalleryPublication :: get_table_name()) . '.id = ' . $this->get_alias('publication_user') . '.publication_id';
        $query .= ' LEFT JOIN ' . $this->escape_table_name('publication_group') . ' AS ' . $this->get_alias('publication_group') . ' ON ' . $this->get_alias(PhotoGalleryPublication :: get_table_name()) . '.id = ' . $this->get_alias('publication_group') . '.publication_id';
        
        return $this->retrieve_object_set($query, PhotoGalleryPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, PhotoGalleryPublication :: CLASS_NAME);
    }

    function retrieve_shared_photo_gallery_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $query = 'SELECT DISTINCT ' . $this->get_alias(PhotoGalleryPublication :: get_table_name()) . '.* FROM ' . $this->escape_table_name(PhotoGalleryPublication :: get_table_name()) . ' AS ' . $this->get_alias(PhotoGalleryPublication :: get_table_name());
        $query .= ' LEFT JOIN ' . $this->escape_table_name('publication_user') . ' AS ' . $this->get_alias('publication_user') . ' ON ' . $this->get_alias(PhotoGalleryPublication :: get_table_name()) . '.id = ' . $this->get_alias('publication_user') . '.publication_id';
        $query .= ' LEFT JOIN ' . $this->escape_table_name('publication_group') . ' AS ' . $this->get_alias('publication_group') . ' ON ' . $this->get_alias(PhotoGalleryPublication :: get_table_name()) . '.id = ' . $this->get_alias('publication_group') . '.publication_id';
        
        return $this->retrieve_object_set($query, PhotoGalleryPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, PhotoGalleryPublication :: CLASS_NAME);
    }

    //Inherited.
    function update_photo_gallery_publication($calendar_event_publication)
    {
        // Delete target users and groups
        $condition = new EqualityCondition('publication_id', $calendar_event_publication->get_id());
        $this->delete_objects(PhotoGalleryPublicationUser :: get_table_name(), $condition);
        $this->delete_objects(PhotoGalleryPublicationGroup :: get_table_name(), $condition);
        
        // Add updated target users and groups
        if (! $this->create_photo_gallery_publication_users($calendar_event_publication))
        {
            return false;
        }
        
        if (! $this->create_photo_gallery_publication_groups($calendar_event_publication))
        {
            return false;
        }
        
        $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_ID, $calendar_event_publication->get_id());
        return $this->update($calendar_event_publication, $condition);
    }

    //Inherited
    function delete_photo_gallery_publication($calendar_event_publication)
    {
        $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_ID, $calendar_event_publication->get_id());
        return $this->delete(PhotoGalleryPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function delete_photo_gallery_publications($object_id)
    {
        $condition = new EqualityCondition(PhotoGalleryPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        return $this->delete_objects(PhotoGalleryPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function update_photo_gallery_publication_id($publication_attr)
    {
        $where = $this->escape_column_name(PhotoGalleryPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->escape_column_name(PhotoGalleryPublication :: PROPERTY_PROFILE)] = $publication_attr->get_publication_object_id();
        $this->get_connection()->loadModule('Extended');
        if ($this->get_connection()->extended->autoExecute($this->get_table_name(PhotoGalleryPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_photo_gallery_publication($publication)
    {
        if (! $this->create($publication))
        {
            return false;
        }
        
        if (! $this->create_photo_gallery_publication_users($publication))
        {
            return false;
        }
        
        if (! $this->create_photo_gallery_publication_groups($publication))
        {
            return false;
        }
        
        return true;
    }

    function create_photo_gallery_publication_user($publication_user)
    {
        return $this->create($publication_user);
    }

    function create_photo_gallery_publication_users($publication)
    {
        $users = $publication->get_target_users();
        
        foreach ($users as $index => $user_id)
        {
            $publication_user = new PhotoGalleryPublicationUser();
            $publication_user->set_publication($publication->get_id());
            $publication_user->set_user($user_id);
            
            if (! $publication_user->create())
            {
                return false;
            }
        }
        
        return true;
    }

    function create_photo_gallery_publication_group($publication_group)
    {
        return $this->create($publication_group);
    }

    function create_photo_gallery_publication_groups($publication)
    {
        $groups = $publication->get_target_groups();
        
        foreach ($groups as $index => $group_id)
        {
            $publication_group = new PhotoGalleryPublicationGroup();
            $publication_group->set_publication($publication->get_id());
            $publication_group->set_group_id($group_id);
            
            if (! $publication_group->create())
            {
                return false;
            }
        }
        
        return true;
    }

    function retrieve_photo_gallery_publication_target_groups($calendar_event_publication)
    {
        $condition = new EqualityCondition(PhotoGalleryPublicationGroup :: PROPERTY_PUBLICATION, $calendar_event_publication->get_id());
        $groups = $this->retrieve_objects(PhotoGalleryPublicationGroup :: get_table_name(), $condition, null, null, array(), PhotoGalleryPublicationGroup :: CLASS_NAME);
        
        $target_groups = array();
        while ($group = $groups->next_result())
        {
            $target_groups[] = $group->get_group_id();
        }
        
        return $target_groups;
    }

    function retrieve_photo_gallery_publication_target_users($calendar_event_publication)
    {
        $condition = new EqualityCondition(PhotoGalleryPublicationUser :: PROPERTY_PUBLICATION, $calendar_event_publication->get_id());
        $users = $this->retrieve_objects(PhotoGalleryPublicationUser :: get_table_name(), $condition, null, null, array(), PhotoGalleryPublicationUser :: CLASS_NAME);
        
        $target_users = array();
        while ($user = $users->next_result())
        {
            $target_users[] = $user->get_user();
        }
        
        return $target_users;
    }
}
?>
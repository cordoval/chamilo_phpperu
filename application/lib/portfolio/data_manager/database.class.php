<?php
/**
 * $Id: database.class.php 230 2009-11-16 09:29:45Z vanpouckesven $
 * @package application.portfolio.data_manager
 */

require_once dirname(__FILE__) . '/../portfolio_publication.class.php';
require_once dirname(__FILE__) . '/../portfolio_publication_group.class.php';
require_once dirname(__FILE__) . '/../portfolio_publication_user.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 */

class DatabasePortfolioDataManager extends PortfolioDataManager
{
    private $database;

    function initialize()
    {
        $aliases = array();
        $aliases[PortfolioPublication :: get_table_name()] = 'poon';
        $aliases[PortfolioPublicationGroup :: get_table_name()] = 'poup';
        $aliases[PortfolioPublicationUser :: get_table_name()] = 'poer';
        
        $this->database = new Database($aliases);
        $this->database->set_prefix('portfolio_');
    }

	function quote($value)
    {
    	return $this->database->quote($value);
    }
    
    function query($query)
    {
    	return $this->database->query($query);
    }
    
    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function get_next_portfolio_publication_id()
    {
        return $this->database->get_next_id(PortfolioPublication :: get_table_name());
    }

    function create_portfolio_publication($portfolio_publication)
    {
        $succes = $this->database->create($portfolio_publication);
        
        foreach ($portfolio_publication->get_target_groups() as $group)
        {
            $pfpg = new PortfolioPublicationGroup();
            $pfpg->set_portfolio_publication($portfolio_publication->get_id());
            $pfpg->set_group_id($group);
            $succes &= $pfpg->create();
        }
        
        foreach ($portfolio_publication->get_target_users() as $user)
        {
            $pfpg = new PortfolioPublicationUser();
            $pfpg->set_portfolio_publication($portfolio_publication->get_id());
            $pfpg->set_user($user);
            $succes &= $pfpg->create();
        }
        
        return $succes;
    }

    function update_portfolio_publication($portfolio_publication, $delete_targets = true)
    {
        $condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $portfolio_publication->get_id());
        $succes = $this->database->update($portfolio_publication, $condition);
        
        if ($delete_targets)
        {
            $condition = new EqualityCondition(PortfolioPublicationGroup :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication->get_id());
            $succes &= $this->database->delete(PortfolioPublicationGroup :: get_table_name(), $condition);
            
            $condition = new EqualityCondition(PortfolioPublicationUser :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication->get_id());
            $succes &= $this->database->delete(PortfolioPublicationUser :: get_table_name(), $condition);
            
            foreach ($portfolio_publication->get_target_groups() as $group)
            {
                $pfpg = new PortfolioPublicationGroup();
                $pfpg->set_portfolio_publication($portfolio_publication->get_id());
                $pfpg->set_group_id($group);
                $succes &= $pfpg->create();
            }
            
            foreach ($portfolio_publication->get_target_users() as $user)
            {
                $pfpu = new PortfolioPublicationUser();
                $pfpu->set_portfolio_publication($portfolio_publication->get_id());
                $pfpu->set_user($user);
                $succes &= $pfpu->create();
            }
        }
        
        return $succes;
    }

    function delete_portfolio_publication($portfolio_publication)
    {
        $condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $portfolio_publication->get_id());
        $succes = $this->database->delete($portfolio_publication->get_table_name(), $condition);
        
        $condition = new EqualityCondition(PortfolioPublicationGroup :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication->get_id());
        $succes &= $this->database->delete(PortfolioPublicationGroup :: get_table_name(), $condition);
        
        $condition = new EqualityCondition(PortfolioPublicationUser :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication->get_id());
        $succes &= $this->database->delete(PortfolioPublicationUser :: get_table_name(), $condition);
        
        return $succes;
    }

    function count_portfolio_publications($condition = null)
    {
        return $this->database->count_objects(PortfolioPublication :: get_table_name(), $condition);
    }

    function retrieve_portfolio_publication($id)
    {
        $condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(PortfolioPublication :: get_table_name(), $condition);
    }

    function retrieve_portfolio_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(PortfolioPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function create_portfolio_publication_group($portfolio_publication_group)
    {
        return $this->database->create($portfolio_publication_group);
    }

    function delete_portfolio_publication_group($portfolio_publication_group)
    {
        $condition = new EqualityCondition(PortfolioPublicationGroup :: PROPERTY_ID, $portfolio_publication_group->get_id());
        return $this->database->delete($portfolio_publication_group->get_table_name(), $condition);
    }

    function count_portfolio_publication_groups($condition = null)
    {
        return $this->database->count_objects(PortfolioPublicationGroup :: get_table_name(), $condition);
    }

    function retrieve_portfolio_publication_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(PortfolioPublicationGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function create_portfolio_publication_user($portfolio_publication_user)
    {
        return $this->database->create($portfolio_publication_user);
    }

    function delete_portfolio_publication_user($portfolio_publication_user)
    {
        $condition = new EqualityCondition(PortfolioPublicationUser :: PROPERTY_ID, $portfolio_publication_user->get_id());
        return $this->database->delete($portfolio_publication_user->get_table_name(), $condition);
    }

    function count_portfolio_publication_users($condition = null)
    {
        return $this->database->count_objects(PortfolioPublicationUser :: get_table_name(), $condition);
    }

    function retrieve_portfolio_publication_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(PortfolioPublicationUser :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function content_object_is_published($object_id)
    {
        return $this->any_content_object_is_published(array($object_id));
    }

    function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(PortfolioPublication :: PROPERTY_CONTENT_OBJECT, $object_ids);
        return $this->database->count_objects(PortfolioPublication :: get_table_name(), $condition) >= 1;
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $query = 'SELECT ' . $this->database->get_alias('portfolio_publication') . '.*, co.' . $this->database->escape_column_name('title') . ' FROM ' . $this->database->escape_table_name('portfolio_publication') . ' AS ' . $this->database->get_alias('portfolio_publication') . ' JOIN ' . RepositoryDataManager :: get_instance()->get_database()->escape_table_name('content_object') . ' AS lo ON ' . $this->database->get_alias('portfolio_publication') . '.`content_object_id` = lo.`id`';
                $query .= ' WHERE ' . $this->database->get_alias('portfolio_publication') . '.' . $this->database->escape_column_name(PortfolioPublication :: PROPERTY_PUBLISHER) . '=' . $this->quote(Session :: get_user_id());
                
                $order = array();
                for($i = 0; $i < count($order_property); $i ++)
                {
                    if ($order_property[$i] == 'application')
                    {
                    }
                    elseif ($order_property[$i] == 'location')
                    {
                        //$order[] = self :: ALIAS_CONTENT_OBJECT_PUBLICATION_TABLE . '.' . $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_COURSE_ID) . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                    //$order[] = self :: ALIAS_CONTENT_OBJECT_PUBLICATION_TABLE . '.' . $this->database->escape_column_name(ContentObjectPublication :: PROPERTY_TOOL) . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    elseif ($order_property[$i] == 'title')
                    {
                        $order[] = 'co.' . $this->database->escape_column_name('title') . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                    }
                    else
                    {
                        //  $order[] = $this->database->get_alias('portfolio_publication') . '.' . $this->database->escape_column_name($order_property[$i], true) . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                        $order[] = 'co.' . $this->database->escape_column_name('title') . ' ' . ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
                    }
                }
                if (count($order))
                {
                    $query .= ' ORDER BY ' . implode(', ', $order);
                }
            }
        }
        else
        {
            $query = 'SELECT * FROM ' . $this->database->escape_table_name('portfolio_publication') . ' WHERE ' . $this->database->escape_column_name(PortfolioPublication :: PROPERTY_CONTENT_OBJECT) . '=' . $this->quote($object_id);
        }
        $res = $this->query($query);
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record[PortfolioPublication :: PROPERTY_ID]);
            $info->set_publisher_user_id($record[PortfolioPublication :: PROPERTY_PUBLISHER]);
            $info->set_publication_date($record[PortfolioPublication :: PROPERTY_PUBLISHED]);
            $info->set_application('portfolio');
            //TODO: i8n location string
            $info->set_location(Translation :: get('MyPortfolio'));
            $info->set_url('run.php?application=portfolio&go=view_portfolio&user_id=' . Session :: get_user_id() . '&pid=' . $record[PortfolioPublication :: PROPERTY_ID]);
            $info->set_publication_object_id($record[PortfolioPublication :: PROPERTY_CONTENT_OBJECT]);
            
            $publication_attr[] = $info;
        }
        return $publication_attr;
    }

    function get_content_object_publication_attribute($publication_id)
    {
        $query = 'SELECT * FROM ' . $this->database->escape_table_name('portfolio_publication') . ' WHERE ' . $this->database->escape_column_name(PortfolioPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
        $this->database->get_connection()->setLimit(0, 1);
        $res = $this->query($query);
        
        $publication_attr = array();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        
        $publication_attr = new ContentObjectPublicationAttributes();
        $publication_attr->set_id($record[PortfolioPublication :: PROPERTY_ID]);
        $publication_attr->set_publisher_user_id($record[PortfolioPublication :: PROPERTY_PUBLISHER]);
        $publication_attr->set_publication_date($record[PortfolioPublication :: PROPERTY_PUBLISHED]);
        $publication_attr->set_application('portfolio');
        //TODO: i8n location string
        $publication_attr->set_location(Translation :: get('MyPortfolio'));
        $publication_attr->set_url('run.php?application=portfolio&go=view_portfolio&user_id=' . Session :: get_user_id() . '&pid=' . $record[PortfolioPublication :: PROPERTY_ID]);
        $publication_attr->set_publication_object_id($record[PortfolioPublication :: PROPERTY_CONTENT_OBJECT]);
        
        return $publication_attr;
    }

    function count_publication_attributes($type = null, $condition = null)
    {
        $condition = new EqualityCondition(PortfolioPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
        return $this->database->count_objects(PortfolioPublication :: get_table_name(), $condition);
    }

    function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(PortfolioPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
        $publications = $this->retrieve_portfolio_publications($condition);
        
        $succes = true;
        
        while ($publication = $publications->next_result())
        {
            $succes &= $publication->delete();
        }
        
        return $succes;
    }

    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name(PortfolioPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name(PortfolioPublication :: PROPERTY_CONTENT_OBJECT)] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name('portfolio_publication'), $props, MDB2_AUTOQUERY_UPDATE, $where))
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
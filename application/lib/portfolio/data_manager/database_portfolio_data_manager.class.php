<?php
/**
 *
 * @package application.portfolio.data_manager
 */

require_once dirname(__FILE__) . '/../portfolio_data_manager.interface.class.php';
require_once dirname(__FILE__) . '/../portfolio_publication.class.php';
require_once dirname(__FILE__) . '/../portfolio_information.class.php';

//require_once 'MDB2.php';


/**
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Sven Vanpoucke
 */

class DatabasePortfolioDataManager extends Database implements PortfolioDataManagerInterface
{

	function initialize()
	{


		$aliases = array();
		$aliases[PortfolioPublication :: get_table_name()] = 'poon';
		parent :: initialize();
		$this->set_prefix('portfolio_');
	}

	function create_portfolio_publication($portfolio_publication)
	{
		$success = $this->create($portfolio_publication);
		return $success;
	}

	function create_portfolio_information($portfolio_info)
	{
		$success = $this->create($portfolio_info, false);
		return $success;
	}

	function update_portfolio_publication($portfolio_publication, $delete_targets = true)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $portfolio_publication->get_id());
		$success = $this->update($portfolio_publication, $condition);
		return $success;
	}

	function update_portfolio_information($portfolio_information)
	{
		$condition = new EqualityCondition(PortfolioInformation :: PROPERTY_USER_ID, $portfolio_information->get_user_id());
		$success = $this->update($portfolio_information, $condition);
		return $success;
	}

	function delete_portfolio_publication($portfolio_publication)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $portfolio_publication->get_id());
		$success = $this->delete($portfolio_publication->get_table_name(), $condition);

		$condition = new EqualityCondition(PortfolioPublicationGroup :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication->get_id());
		$success &= $this->delete(PortfolioPublicationGroup :: get_table_name(), $condition);

		$condition = new EqualityCondition(PortfolioPublicationUser :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication->get_id());
		$success &= $this->delete(PortfolioPublicationUser :: get_table_name(), $condition);

		return $success;
	}

	function count_portfolio_publications($condition = null)
	{
		return $this->count_objects(PortfolioPublication :: get_table_name(), $condition);
	}

	function retrieve_portfolio_publication($id)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $id);
		return $this->retrieve_object(PortfolioPublication :: get_table_name(), $condition);
	}

	function retrieve_portfolio_information_by_user($user_id)
	{
		$condition = new EqualityCondition(PortfolioInformation :: PROPERTY_USER_ID, $user_id);
		return $this->retrieve_object(PortfolioInformation :: get_table_name(), $condition);
	}

	function retrieve_portfolio_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(PortfolioPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function create_portfolio_publication_group($portfolio_publication_group)
	{
		return $this->create($portfolio_publication_group);
	}

	function delete_portfolio_publication_group($portfolio_publication_group)
	{
		$condition = new EqualityCondition(PortfolioPublicationGroup :: PROPERTY_ID, $portfolio_publication_group->get_id());
		return $this->delete($portfolio_publication_group->get_table_name(), $condition);
	}

	function count_portfolio_publication_groups($condition = null)
	{
		return $this->count_objects(PortfolioPublicationGroup :: get_table_name(), $condition);
	}

	function retrieve_portfolio_publication_groups($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(PortfolioPublicationGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function create_portfolio_publication_user($portfolio_publication_user)
	{
		return $this->create($portfolio_publication_user);
	}

	function delete_portfolio_publication_user($portfolio_publication_user)
	{
		$condition = new EqualityCondition(PortfolioPublicationUser :: PROPERTY_ID, $portfolio_publication_user->get_id());
		return $this->delete($portfolio_publication_user->get_table_name(), $condition);
	}

	function count_portfolio_publication_users($condition = null)
	{
		return $this->count_objects(PortfolioPublicationUser :: get_table_name(), $condition);
	}

	function retrieve_portfolio_publication_users($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(PortfolioPublicationUser :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function content_object_is_published($object_id)
	{
		return $this->any_content_object_is_published(array($object_id));
	}

	function any_content_object_is_published($object_ids)
	{
		$condition = new InCondition(PortfolioPublication :: PROPERTY_CONTENT_OBJECT, $object_ids);
		return $this->count_objects(PortfolioPublication :: get_table_name(), $condition) >= 1;
	}

	function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_properties = null)
	{
		if (isset($type))
		{
			if ($type == 'user')
			{
				$rdm = RepositoryDataManager :: get_instance();
				$co_alias = $rdm->get_alias(ContentObject :: get_table_name());
				$pub_alias = $this->get_alias(PortfolioPublication :: get_table_name());

				$query = 'SELECT ' . $pub_alias . '.*, ' . $co_alias . '.' . $this->escape_column_name(ContentObject :: PROPERTY_TITLE) . ' FROM ' . $this->escape_table_name(PortfolioPublication :: get_table_name()) . ' AS ' . $pub_alias . ' JOIN ' . $rdm->escape_table_name(ContentObject :: get_table_name()) . ' AS ' . $co_alias . ' ON ' . $this->escape_column_name(PortfolioPublication :: PROPERTY_CONTENT_OBJECT, $pub_alias) . '=' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $co_alias);

				$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
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
			$query = 'SELECT * FROM ' . $this->escape_table_name(PortfolioPublication :: get_table_name());
			$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
			$translator = new ConditionTranslator($this);
			$query .= $translator->render_query($condition);

		}

		$this->set_limit($offset, $count);
		$res = $this->query($query);

		$publication_attr = array();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$info = new ContentObjectPublicationAttributes();
			$info->set_id($record[PortfolioPublication :: PROPERTY_ID]);
			$info->set_publisher_user_id($record[PortfolioPublication :: PROPERTY_PUBLISHER]);
			$info->set_publication_date($record[PortfolioPublication :: PROPERTY_PUBLISHED]);
			$info->set_application(PortfolioManager :: APPLICATION_NAME);
			//TODO: i8n location string
			$info->set_location(Translation :: get('MyPortfolio'));
			$info->set_url('run.php?application=portfolio&go=view_portfolio&user_id=' . Session :: get_user_id() . '&pid=' . $record[PortfolioPublication :: PROPERTY_ID]);
			$info->set_publication_object_id($record[PortfolioPublication :: PROPERTY_CONTENT_OBJECT]);

			$publication_attr[] = $info;
		}

		$res->free();

		return $publication_attr;
	}

	function get_content_object_publication_attribute($publication_id)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name('portfolio_publication') . ' WHERE ' . $this->database->escape_column_name(PortfolioPublication :: PROPERTY_ID) . '=' . $this->quote($publication_id);
		$this->get_connection()->setLimit(0, 1);
		$res = $this->query($query);

		$publication_attr = array();
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		$res->free();

		$publication_attr = new ContentObjectPublicationAttributes();
		$publication_attr->set_id($record[PortfolioPublication :: PROPERTY_ID]);
		$publication_attr->set_publisher_user_id($record[PortfolioPublication :: PROPERTY_PUBLISHER]);
		$publication_attr->set_publication_date($record[PortfolioPublication :: PROPERTY_PUBLISHED]);
		$publication_attr->set_application(PortfolioManager :: APPLICATION_NAME);
		//TODO: i8n location string
		$publication_attr->set_location(Translation :: get('MyPortfolio'));
		$publication_attr->set_url('run.php?application=portfolio&go=view_portfolio&user_id=' . Session :: get_user_id() . '&pid=' . $record[PortfolioPublication :: PROPERTY_ID]);
		$publication_attr->set_publication_object_id($record[PortfolioPublication :: PROPERTY_CONTENT_OBJECT]);

		return $publication_attr;
	}

	function count_publication_attributes($user = null, $object_id = null, $condition = null)
	{
		if (! $object_id)
		{
			$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_PUBLISHER, $user->get_id());
		}
		else
		{
			$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
		}
		return $this->count_objects(PortfolioPublication :: get_table_name(), $condition);
	}

	function delete_content_object_publications($object_id)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_CONTENT_OBJECT, $object_id);
		return $this->delete(PortfolioPublication :: get_table_name(), $condition);
	}

	function delete_content_object_publication($publication_id)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $publication_id);
		return $this->delete(PortfolioPublication :: get_table_name(), $condition);
	}

	function update_content_object_publication_id($publication_attr)
	{
		$where = $this->escape_column_name(PortfolioPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
		$props = array();
		$props[$this->escape_column_name(PortfolioPublication :: PROPERTY_CONTENT_OBJECT)] = $publication_attr->get_publication_object_id();
		$this->get_connection()->loadModule('Extended');
		if ($this->get_connection()->extended->autoExecute($this->get_table_name('portfolio_publication'), $props, MDB2_AUTOQUERY_UPDATE, $where))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * returns the publisher (owner) of a portfolio item
	 * @param cid: id of the portfolio item (= complex content object item)
	 * @return: user_id of publisher
	 */
	public function retrieve_portfolio_item_user($cid)
	{
		$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_ID, $cid);
		$item = RepositoryManager :: retrieve_complex_content_object_item($cid);
		return $item->get_user_id();
	}


	/**
	 * returns the owner of a portfolio item
	 * @param cid: id of the portfolio item (= complex content object item)
	 * @return: user_id of owner
	 */
	public function retrieve_portfolio_item_owner($cid)
	{
		//TODO: maybe easier via the location?!
	}
	/**
	 * returns the publisher (owner) of a portfolio publication
	 * @param pid: id of the portfolio publication
	 * @return: user_id of publisher
	 */
	function retrieve_portfolio_publication_user($pid)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $pid);
		$item = $this->retrieve_object(PortfolioPublication :: get_table_name(), $condition);
		return $item->get_publisher();
	}

	/**
	 * returns the owner  of a portfolio publication
	 * @param pid: id of the portfolio publication
	 * @return: user_id of owner
	 */
	function retrieve_portfolio_publication_owner($pid)
	{
		$condition = new EqualityCondition(PortfolioPublication :: PROPERTY_ID, $pid);
		$item = $this->retrieve_object(PortfolioPublication :: get_table_name(), $condition);
		return $item->get_owner();
	}

	public function retrieve_portfolio_children($content_object_id)
	{
		$condition = new EqualityCondition(ComplexContentObjectItem::PROPERTY_PARENT, $content_object_id);
		$rdm = RepositoryDataManager::get_instance();
		$object_set = $rdm->retrieve_objects(ComplexContentObjectItem::get_table_name(), $condition);
		return $object_set;


	}

	public function get_portfolio_children($portfolio_id) {
		return self::retrieve_portfolio_children($portfolio_id);
	}


}
?>
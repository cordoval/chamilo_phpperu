<?php
/**
 * $Id: portfolio_data_manager.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio
 */
/**
 *	This is a skeleton for a data manager for the Portfolio Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Sven Vanpoucke
 */
abstract class PortfolioDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function PortfolioDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return PortfolioDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
            $class = $type . 'PortfolioDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function initialize();

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function create_portfolio_publication($portfolio_publication);

    abstract function update_portfolio_publication($portfolio_publication);

    abstract function delete_portfolio_publication($portfolio_publication);

    abstract function count_portfolio_publications($conditions = null);

    abstract function retrieve_portfolio_publication($id);

    abstract function retrieve_portfolio_publications($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_portfolio_publication_group($portfolio_publication_group);

    abstract function delete_portfolio_publication_group($portfolio_publication_group);

    abstract function count_portfolio_publication_groups($conditions = null);

    abstract function retrieve_portfolio_publication_groups($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function create_portfolio_publication_user($portfolio_publication_user);

    abstract function delete_portfolio_publication_user($portfolio_publication_user);

    abstract function count_portfolio_publication_users($conditions = null);

    abstract function retrieve_portfolio_publication_users($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function content_object_is_published($object_id);

    abstract function any_content_object_is_published($object_ids);

    abstract function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null);

    abstract function get_content_object_publication_attribute($publication_id);

    abstract function count_publication_attributes($user = null, $object_id = null, $condition = null);

    abstract function delete_content_object_publications($object_id);

    abstract function update_content_object_publication_id($publication_attr);

}
?>
<?php
/**
 * $Id: portfolio_data_manager.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio
 */
/**
 *	This is a skeleton for a data manager for the Portfolio Application.
 *	Data managers must extend this class and implement its methods.
 *
 *	@author Sven Vanpoucke
 */
interface PortfolioDataManagerInterface
{

    function initialize();
   
    function create_storage_unit($name, $properties, $indexes);

    function create_portfolio_publication($portfolio_publication);

    function update_portfolio_publication($portfolio_publication);
    function update_portfolio_information($portfolio_information);
    function delete_portfolio_publication($portfolio_publication);

    function count_portfolio_publications($conditions = null);
    function retrieve_portfolio_publication_user($pid);
    function retrieve_portfolio_publication($pid);
     function retrieve_portfolio_item_user($cid);
    function retrieve_portfolio_information_by_user($user_id);
    function retrieve_portfolio_publications($condition = null, $offset = null, $count = null, $order_property = null);
    function create_portfolio_information($portfolio_publication);
    function create_portfolio_publication_group($portfolio_publication_group);

    function delete_portfolio_publication_group($portfolio_publication_group);

    function count_portfolio_publication_groups($conditions = null);

    function retrieve_portfolio_publication_groups($condition = null, $offset = null, $count = null, $order_property = null);

    function create_portfolio_publication_user($portfolio_publication_user);

    function delete_portfolio_publication_user($portfolio_publication_user);

    function count_portfolio_publication_users($conditions = null);

    function retrieve_portfolio_publication_users($condition = null, $offset = null, $count = null, $order_property = null);

    function content_object_is_published($object_id);

    function any_content_object_is_published($object_ids);

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null);

    function get_content_object_publication_attribute($publication_id);

    function count_publication_attributes($user = null, $object_id = null, $condition = null);

    function delete_content_object_publications($object_id);

    function update_content_object_publication_id($publication_attr);

}
?>
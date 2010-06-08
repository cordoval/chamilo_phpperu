<?php
/**
 * $Id: distribute_data_manager.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute
 */
/**
 * This is a skeleton for a data manager for the Distribute Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Hans De Bisschop
 */
interface DistributeDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_announcement_distribution($announcement_distribution);

    function update_announcement_distribution($announcement_distribution);

    function delete_announcement_distribution($announcement_distribution);

    function count_announcement_distributions($conditions = null);

    function retrieve_announcement_distribution($id);

    function retrieve_announcement_distributions($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_announcement_distribution_target_groups($announcement_distribution);

    function retrieve_announcement_distribution_target_users($announcement_distribution);
}
?>
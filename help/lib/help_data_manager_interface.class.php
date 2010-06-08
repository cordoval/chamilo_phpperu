<?php
/**
 * @package help.lib
 *
 * This is an interface for a data manager for the Help application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface HelpDataManagerInterface
{
    function initialize();

    function update_help_item($help_item);

    function create_help_item($help_item);

    function count_help_items($conditions = null);

    function retrieve_help_item($id);

    function retrieve_help_items($condition = null, $offset = null, $count = null, $order_property = null);

    function create_storage_unit($name, $properties, $indexes);

    function retrieve_help_item_by_name_and_language($name, $language);

}
?>
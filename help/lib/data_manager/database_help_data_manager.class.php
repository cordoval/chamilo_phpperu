<?php
/**
 * $Id: database_help_data_manager.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.data_manager
 */
require_once 'MDB2.php';
require_once dirname(__FILE__) . '/../help_data_manager_interface.class.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
 *  @author Sven Vanpoucke
 *  @author Hans De Bisschop
==============================================================================
 */

class DatabaseHelpDataManager extends DataManager implements HelpDataManagerInterface
{

    function initialize()
    {
        $this->set_prefix('help_');
    }

    function update_help_item($help_item)
    {
        $condition = new EqualityCondition(HelpItem :: PROPERTY_ID, $help_item->get_id());
        return $this->update($help_item, $condition);
    }

    function create_help_item($help_item)
    {
        return $this->create($help_item);
    }

    function count_help_items($condition = null)
    {
        return $this->count_objects(HelpItem :: get_table_name(), $condition);
    }

    function retrieve_help_items($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(HelpItem :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_help_item($id)
    {
        $condition = new EqualityCondition(HelpItem :: PROPERTY_ID, $id);
        return $this->retrieve_object(HelpItem :: get_table_name(), $condition);
    }

    function retrieve_help_item_by_name_and_language($name, $language)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(HelpItem :: PROPERTY_NAME, $name);
        $conditions[] = new EqualityCondition(HelpItem :: PROPERTY_LANGUAGE, $language);

        $condition = new AndCondition($conditions);

        return $this->retrieve_object(HelpItem :: get_table_name(), $condition);
    }
}
?>
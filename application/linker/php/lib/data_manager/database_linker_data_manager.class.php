<?php
namespace application\linker;

use common\libraries\Database;
/**
 * $Id: database_linker_data_manager.class.php 199 2009-11-13 12:23:04Z chellee $
 * @package application.lib.linker.data_manager
 */
/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
==============================================================================
 */

class DatabaseLinkerDataManager extends Database implements LinkerDataManagerInterface
{
    /**
     * Initialize
     */
    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('link_');
    }

    function update_linker($link)
    {
        $condition = new EqualityCondition(Linker :: PROPERTY_ID, $link->get_id());
        return $this->update($link, $condition);
    }

    function delete_linker($link)
    {
        $condition = new EqualityCondition(Linker :: PROPERTY_ID, $link->get_id());
        return $this->delete($link->get_table_name(), $condition);
    }

    function create_linker($link)
    {
    	return $this->create($link);
    }

    function count_links($condition = null)
    {
        return $this->count_objects(Linker :: get_table_name(), $condition);
    }

    function retrieve_links($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(Linker :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_link($id)
    {
        $condition = new EqualityCondition(Linker :: PROPERTY_ID, $id);
        return $this->retrieve_object(Linker :: get_table_name(), $condition);
    }
}
?>
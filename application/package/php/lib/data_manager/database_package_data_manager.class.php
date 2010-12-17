<?php

namespace application\package;

use common\libraries\Database;
use common\libraries\EqualityCondition;
use common\libraries\SubselectCondition;
use common\libraries\AndCondition;
use common\libraries\InCondition;
use user\UserDataManager;
use user\User;
/**
 * @package package.datamanager
 */

/**
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class DatabasePackageDataManager extends Database implements PackageDataManagerInterface
{
    /*
	 * Helper variable so we don't need to make subselects each and every row
	 */
    private $variable_ids;

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('package_');
    }

    function get_next_package_id()
    {
        return $this->get_next_id(Package :: get_table_name());
    }

    function count_packages($condition = null)
    {
        return $this->count_objects(Package :: get_table_name(), $condition);
    }

    function retrieve_package($id)
    {
        $condition = new EqualityCondition(Package :: PROPERTY_ID, $id);
        return $this->retrieve_object(Package :: get_table_name(), $condition, null, Package :: CLASS_NAME);
    }

    function retrieve_packages($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(Package :: get_table_name(), $condition, $offset, $max_objects, $order_by, Package :: CLASS_NAME);
    }
    
    function retrieve_author($id)
    {
        $condition = new EqualityCondition(Author :: PROPERTY_ID, $id);
        return $this->retrieve_object(Author :: get_table_name(), $condition, null, Author :: CLASS_NAME);
    }

    function count_authors($condition = null)
    {
        return $this->count_objects(Author :: get_table_name(), $condition);
    }

    function retrieve_authors($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(Author :: get_table_name(), $condition, $offset, $max_objects, $order_by, Author :: CLASS_NAME);
    }

}
?>
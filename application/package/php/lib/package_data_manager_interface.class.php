<?php

namespace application\package;
/**
 * This is a skeleton for a data manager for the Package Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

interface PackageDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function get_next_package_id();

    function count_packages($conditions = null);

    function retrieve_package($id);

    function retrieve_packages($condition = null, $offset = null, $count = null, $order_property = null);
    
    function count_authors($conditions = null);

    function retrieve_authors($condition = null, $offset = null, $count = null, $order_property = null);
    
    function retrieve_author($id);
    
    function count_dependencies($conditions = null);
    
    function retrieve_dependencies($condition = null, $offset = null, $count = null, $order_property = null);
    
    function retrieve_dependency($id);
}
?>
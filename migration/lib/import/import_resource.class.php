<?php

/**
 * $Id: import_resource.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a resource
 * @author Sven Vanpoucke
 */
abstract class ImportResource extends Import
{

    abstract function is_valid_resource($course);

    abstract function convert_to_new_resource($course);

    abstract static function get_all($parameters);
}

?>
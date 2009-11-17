<?php

/**
 * $Id: import_class.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a class
 * @author Sven Vanpoucke
 */
abstract class ImportClass extends Import
{

    abstract function is_valid($parameters);

    abstract function convert_to_lcms($parameters);

    abstract static function get_all($parameters);
}
?>

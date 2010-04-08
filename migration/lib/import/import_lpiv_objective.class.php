<?php
/**
 * $Id: import_lpiv_objective.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a learning path item view objective
 * @author Sven Vanpoucke
 */
abstract class ImportLpIVObjective extends Import
{

    abstract function is_valid($array);

    abstract function convert_to_lcms($array);

    abstract static function get_all($array);
}
?>
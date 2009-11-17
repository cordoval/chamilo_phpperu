<?php

/**
 * $Id: import_group_rel_user.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a group user relation
 * @author Sven Vanpoucke
 */
abstract class ImportGroupRelUser extends Import
{

    abstract function is_valid($course);

    abstract function convert_to_lcms($course);

    abstract static function get_all($parameters);
}
?>
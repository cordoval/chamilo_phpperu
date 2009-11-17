<?php

/**
 * $Id: import_course_rel_user.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a course user
 * @author Sven Vanpoucke
 */
abstract class ImportCourseRelUser extends Import
{

    abstract function is_valid($parameters);

    abstract function convert_to_lcms($parameters);

    abstract static function get_all($parameters);
}
?>
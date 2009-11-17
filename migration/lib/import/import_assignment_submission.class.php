<?php
/**
 * $Id: import_assignment_submission.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a assignment submission
 * @author Sven Vanpoucke
 */
abstract class ImportAssignmentSubmission extends Import
{

    abstract function is_valid($array);

    abstract function convert_to_lcms($array);

    abstract static function get_all($array);
}
?>
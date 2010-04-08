<?php
/**
 * $Id: import_blog_comment.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a blog comment
 * @author Sven Vanpoucke
 */
abstract class ImportBlogComment extends Import
{

    abstract function is_valid($array);

    abstract function convert_to_lcms($array);

    abstract static function get_all($array);
}
?>
<?php

/**
 * $Id: import_dropbox_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a dropbox category
 * @author Van Wayenbergh David
 */
abstract class ImportDropboxCategory extends Import
{

    abstract function is_valid($array);

    abstract function convert_to_lcms($array);

    abstract static function get_all($array);
}
?>
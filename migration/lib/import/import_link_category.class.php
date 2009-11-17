<?php

/**
 * $Id: import_link_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a link category
 * @author Van Wayenbergh David
 */

abstract class ImportLinkCategory extends Import
{

    abstract function is_valid($parameters);

    abstract function convert_to_lcms($parameters);

    abstract static function get_all($parameters);
}
?>
<?php

/**
 * $Id: import_unknown_data.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.import
 */

/**
 * Abstract class that defines unknow data
 * @author Van Wayenbergh David
 */

abstract class ImportUnknownData
{

    abstract function is_valid_unknow_data();

    abstract function convert_to_content_object();

    abstract static function get_all($parameters);
}

?>
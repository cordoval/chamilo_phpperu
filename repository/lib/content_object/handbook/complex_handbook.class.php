<?php
/**
 * $Id: complex_handbook.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.handbook
 */

class ComplexHandbook extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array(Handbook :: get_type_name(), HandbookItem :: get_type_name());
    }
}
?>
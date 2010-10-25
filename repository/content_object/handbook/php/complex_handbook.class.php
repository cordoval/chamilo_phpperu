<?php


class ComplexHandbook extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array(Handbook :: get_type_name(), HandbookItem :: get_type_name());
    }
}
?>
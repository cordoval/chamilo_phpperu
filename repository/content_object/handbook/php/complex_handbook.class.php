<?php
namespace repository\content_object\handbook;

use repository\ComplexContentObjectItem;
use HandbookItem;


class ComplexHandbook extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array(Handbook :: get_type_name(), HandbookItem :: get_type_name());
    }
}
?>
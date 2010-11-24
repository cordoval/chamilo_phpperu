<?php
namespace repository\content_object\handbook;

use repository\ComplexContentObjectItem;
use repository\content_object\handbook_item\HandbookItem;


class ComplexHandbook extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array(Handbook :: get_type_name(), HandbookItem :: get_type_name());
    }
}
?>
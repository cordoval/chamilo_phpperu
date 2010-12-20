<?php
namespace repository\content_object\glossary;

use repository\content_object\glossary_item\GlossaryItem;

use repository\ComplexContentObjectItem;

/**
 * $Id: complex_glossary.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.glossary
 */

class ComplexGlossary extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array(GlossaryItem :: get_type_name());
    }
}
?>
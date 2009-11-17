<?php
/**
 * $Id: complex_glossary.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.glossary
 */

class ComplexGlossary extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array('glossary_item');
    }
}
?>
<?php
/**
 * $Id: glossary.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.glossary
 */
/**
 * This class represents an glossary
 */
class Glossary extends ContentObject
{

    function get_allowed_types()
    {
        return array('glossary_item');
    }
}
?>
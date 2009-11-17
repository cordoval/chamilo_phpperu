<?php
/**
 * $Id: note.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.note
 */
/**
 * This class represents an note
 */
class Note extends ContentObject
{

    //Inherited
    function supports_attachments()
    {
        return true;
    }
}
?>
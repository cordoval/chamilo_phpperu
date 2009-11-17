<?php
/**
 * $Id: personal_message.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.personal_message
 */
/**
 * This class represents a personal message
 */
class PersonalMessage extends ContentObject
{

    //Inherited
    function supports_attachments()
    {
        return true;
    }

    function is_versionable()
    {
        return true;
    }

    function is_versioning_required()
    {
        return true;
    }
}
?>
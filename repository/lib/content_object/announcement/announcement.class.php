<?php
/**
 * $Id: announcement.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.announcement
 *
 */
/**
 * This class represents an announcement
 */
class Announcement extends ContentObject
{

    //Inherited
    function supports_attachments()
    {
        return true;
    }
}
?>
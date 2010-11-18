<?php
namespace repository\content_object\announcement;

use repository\ContentObjectForm;

/**
 * $Id: announcement_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.announcement
 *
 */
require_once dirname(__FILE__) . '/announcement.class.php';
/**
 * This class represents a form to create or update announcements
 */
class AnnouncementForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new Announcement();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
?>
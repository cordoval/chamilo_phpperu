<?php
namespace repository\content_object\note;

use repository\ContentObjectForm;

/**
 * $Id: note_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.note
 */
require_once dirname(__FILE__) . '/note.class.php';
/**
 * This class represents a form to create or update notes
 */
class NoteForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new Note();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
?>
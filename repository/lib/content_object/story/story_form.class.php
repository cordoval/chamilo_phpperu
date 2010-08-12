<?php
/**
 * This class describes the form for a Story object.
 * @package repository.lib.content_object.link
 * @author Hans De Bisschop
 **/

require_once dirname(__FILE__) . '/story.class.php';

class StoryForm extends ContentObjectForm
{

    function create_content_object()
    {
        $object = new Story();
        parent :: set_content_object($object);
        return parent :: create_content_object();
    }
}
?>
<?php
namespace repository\content_object\learning_path;

use repository\ContentObjectForm;

/**
 * $Id: learning_path_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.learning_path
 */
require_once dirname(__FILE__) . '/learning_path.class.php';

class LearningPathForm extends ContentObjectForm
{

    function create_content_object()
    {
        $object = new LearningPath();
        $object->set_version('chamilo');
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
?>
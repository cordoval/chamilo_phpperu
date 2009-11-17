<?php
/**
 * $Id: complex_learning_path.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.learning_path
 */

class ComplexLearningPath extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array('learning_path', 'learning_path_item');
    }
}
?>
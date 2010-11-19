<?php
namespace repository\content_object\learning_path;

use common\libraries\Path;
use repository\ComplexBuilderComponent;

/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
class LearningPathBuilderDeleterComponent extends LearningPathBuilder
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>
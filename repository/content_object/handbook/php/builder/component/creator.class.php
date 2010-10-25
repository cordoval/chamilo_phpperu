<?php
/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
require_once dirname(__FILE__) . '/../handbook_builder.class.php';

/**
 */
class HandbookBuilderCreatorComponent extends HandbookBuilder
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
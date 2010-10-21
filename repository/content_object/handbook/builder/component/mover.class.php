<?php
/**
 * $Id: mover.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../handbook_builder.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class HandbookBuilderMoverComponent extends HandbookBuilder
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
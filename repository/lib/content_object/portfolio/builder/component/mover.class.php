<?php
/**
 * $Id: mover.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../portfolio_builder.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class PortfolioBuilderMoverComponent extends PortfolioBuilder
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $component = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: MOVER_COMPONENT, $this);
        $component->run();
    }
}
?>
<?php
/**
 * $Id: updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../portfolio_builder.class.php';

class PortfolioBuilderParentChangerComponent extends PortfolioBuilder
{
    const PARAM_NEW_PARENT = 'new_parent';

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>
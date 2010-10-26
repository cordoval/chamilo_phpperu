<?php
namespace repository\content_object\portfolio;

use repository\ComplexBuilderComponent;

require_once dirname(__FILE__) . '/../portfolio_builder.class.php';

/**
 */
class PortfolioBuilderCreatorComponent extends PortfolioBuilder
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
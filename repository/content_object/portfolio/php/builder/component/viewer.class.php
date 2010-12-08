<?php
namespace repository\content_object\portfolio;

use repository\ComplexBuilderComponent;


class PortfolioBuilderViewerComponent extends PortfolioBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>
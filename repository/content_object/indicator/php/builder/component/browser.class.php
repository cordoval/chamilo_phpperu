<?php
namespace repository\content_object\indicator;

use repository\ComplexBuilderComponent;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Application;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.indicator.component
 */

class IndicatorBuilderBrowserComponent extends IndicatorBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

<?php
namespace repository\content_object\competence;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Application;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.indicator.component
 */
//require_once dirname(__FILE__) . '/../indicator_builder_component.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class CompetenceBuilderBrowserComponent extends CompetenceBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>
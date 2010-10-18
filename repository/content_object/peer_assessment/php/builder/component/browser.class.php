<?php
namespace repository\content_object\peer_assessment;

use common\libraries\Path;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.indicator.component
 */
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class PeerAssessmentBuilderBrowserComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>
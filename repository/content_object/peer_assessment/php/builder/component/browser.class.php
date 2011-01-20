<?php
namespace repository\content_object\peer_assessment;


/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.indicator.component
 */

use repository\ComplexBuilderComponent;

class PeerAssessmentBuilderBrowserComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
